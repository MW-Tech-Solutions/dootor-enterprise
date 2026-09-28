<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RequestDocument;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $drafts = $user->clientRequests()
            ->whereIn('status', ['Draft', 'In Progress'])
            ->latest('updated_at')
            ->get();

        $requests = $user->clientRequests()
            ->whereNotIn('status', ['Draft', 'In Progress'])
            ->latest()
            ->take(10)
            ->get();

        return view('client.dashboard', [
            'drafts' => $drafts,
            'requests' => $requests,
            'total_requests_count' => $user->clientRequests()->count(),
            'paid_requests_count' => $user->clientRequests()->where('payment_status', 'Paid')->count(),
            'unpaid_requests_count' => $user->clientRequests()->where('payment_status', 'Unpaid')->count(),
        ]);
    }

    public function services()
    {
        $primaryServices = Service::where('status', 'Active')
            ->whereNull('parent_id')
            ->where(function ($q) {
                $q->where('is_primary', true)->orWhere('is_primary', 1);
            })
            ->with(['subServices' => function ($q) {
                $q->where('status', 'Active');
            }])
            ->get();

        // Fail-safe fallback: If database records have is_primary = 0, treat all top-level services (parent_id IS NULL) as primary services
        if ($primaryServices->isEmpty()) {
            $primaryServices = Service::where('status', 'Active')
                ->whereNull('parent_id')
                ->with(['subServices' => function ($q) {
                    $q->where('status', 'Active');
                }])
                ->get();

            $otherServices = collect();
        } else {
            $otherServices = Service::where('status', 'Active')
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('is_primary', false)->orWhere('is_primary', 0);
                })
                ->with(['subServices' => function ($q) {
                    $q->where('status', 'Active');
                }])
                ->get();
        }

        $settings = SystemSetting::first();
        $currencyCode = strtoupper((string) ($settings->default_currency ?? config('services.payment.currency', 'NGN')));
        $currencySymbol = match($currencyCode) {
            'NGN' => '₦',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'CAD' => 'CA$',
            default => $currencyCode . ' ',
        };

        return view('client.services', compact('primaryServices', 'otherServices', 'settings', 'currencyCode', 'currencySymbol'));
    }

    public function requests()
    {
        $user = Auth::user();
        $requests = $user->clientRequests()->with(['requestDocuments', 'assignedStaff'])->latest()->get();
        return view('client.requests', ['requests' => $requests]);
    }

    public function requestDetails(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $serviceRequest->load([
            'requestDocuments',
            'assignedStaff',
            'assignedRole',
            'service.workflowStages',
            'currentStage',
            'stageHistories.changedBy',
            'applicationNotes.user',
        ]);

        return view('client.request-details', ['request' => $serviceRequest]);
    }

    public function book(\App\Models\Service $service)
    {
        if ($service->status !== 'Active') {
            abort(404, 'Service is not active');
        }

        $client = Auth::user();

        // Check if an unfinished draft for this service already exists for the client
        $existingDraft = ServiceRequest::where('client_id', $client->id)
            ->where(function ($q) use ($service) {
                $q->where('service_id', $service->id)
                  ->orWhere('sub_service_id', $service->id);
            })
            ->whereIn('status', ['Draft', 'In Progress'])
            ->latest()
            ->first();

        if ($existingDraft) {
            $step = max(1, (int) ($existingDraft->current_step ?? 1));
            return redirect()->route('client.application.step', ['serviceRequest' => $existingDraft->id, 'step' => $step])
                ->with('info', 'Restored your active draft application (' . $existingDraft->reference_number . ').');
        }

        // Auto-create draft application on first initiation (Requirement #6)
        $totalPrice = (float) ($service->price + $service->service_fee + $service->processing_fee);
        $admin = User::where('role', 'admin')->first() ?? $client;
        $parentService = $service->parent_id ? Service::find($service->parent_id) : $service;
        $subService = $service->parent_id ? $service : null;
        $referenceNumber = \App\Services\ReferenceNumberGenerator::generate($parentService->id);

        $draft = ServiceRequest::create([
            'reference_number' => $referenceNumber,
            'application_method' => 'online',
            'service_id' => $parentService->id,
            'sub_service_id' => $subService?->id,
            'service_name' => $parentService->name,
            'sub_service_name' => $subService?->name,
            'price' => $totalPrice,
            'amount_paid' => 0,
            'outstanding_balance' => $totalPrice,
            'vendor_id' => $admin->id,
            'vendor_name' => $admin->name ?? 'Dooter Enterprises Admin',
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_email' => $client->email,
            'country_applying_from' => $client->country_applying_from ?? $client->country,
            'country_service_requested' => $client->country_service_requested ?? 'Nigeria',
            'status' => 'Draft',
            'current_step' => 1,
            'progress_percent' => 25,
            'payment_status' => 'Unpaid',
            'form_data' => [
                'first_name' => $client->first_name,
                'middle_name' => $client->middle_name,
                'last_name' => $client->last_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'country_applying_from' => $client->country_applying_from ?? $client->country,
                'state' => $client->state,
                'city' => $client->city,
                'country_service_requested' => $client->country_service_requested ?? 'Nigeria',
            ],
            'last_saved_at' => now(),
        ]);

        \App\Services\AuditLogger::log('draft_application_created', 'ServiceRequest', (string) $draft->id, $draft->reference_number, null, [
            'service_name' => $draft->service_name,
            'sub_service_name' => $draft->sub_service_name,
        ]);

        return redirect()->route('client.application.step', ['serviceRequest' => $draft->id, 'step' => 1]);
    }

    // Step View Handler
    public function applicationStep(ServiceRequest $serviceRequest, int $step)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $step = max(1, min(4, $step));

        $serviceRequest->update([
            'current_step' => $step,
            'progress_percent' => $step * 25,
            'last_saved_at' => now(),
        ]);

        $serviceRequest->load([
            'service.fields' => function ($q) {
                $q->where('is_enabled', true)->orderBy('sort_order', 'asc');
            },
            'subService',
            'requestDocuments',
        ]);

        return view('client.application-step', [
            'application' => $serviceRequest,
            'step' => $step,
            'service' => $serviceRequest->subService ?? $serviceRequest->service,
            'allCountries' => \App\Services\LocationService::allCountries(),
        ]);
    }

    // Background Auto-Save Endpoint (Requirement #7)
    public function autoSave(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $inputData = $request->input('form_data', []);
        $existingData = is_array($serviceRequest->form_data) ? $serviceRequest->form_data : [];
        $mergedData = array_merge($existingData, $inputData);

        $step = (int) $request->input('current_step', $serviceRequest->current_step);
        $progress = (int) $request->input('progress_percent', $step * 25);

        $serviceRequest->update([
            'form_data' => $mergedData,
            'current_step' => $step,
            'progress_percent' => $progress,
            'country_applying_from' => $request->input('country_applying_from', $serviceRequest->country_applying_from),
            'country_service_requested' => $request->input('country_service_requested', $serviceRequest->country_service_requested),
            'status' => in_array($serviceRequest->status, ['Draft', 'In Progress']) ? 'In Progress' : $serviceRequest->status,
            'last_saved_at' => now(),
        ]);

        // Intelligent Throttled Admin Notification for Incomplete Drafts (Requirement #13)
        if (
            in_array($serviceRequest->status, ['Draft', 'In Progress']) &&
            (empty($serviceRequest->admin_notified_draft_at) || $serviceRequest->admin_notified_draft_at->diffInMinutes(now()) >= 30)
        ) {
            $uploadedCount = $serviceRequest->requestDocuments()->count();
            $adminEmail = config('mail.from.address', 'admin@dootor-enterprises.com');

            \App\Services\EmailNotificationService::send('draft_incomplete_admin', $adminEmail, [
                'full_name' => $serviceRequest->client_name,
                'service_name' => $serviceRequest->service_name,
                'sub_service_name' => $serviceRequest->sub_service_name ?? 'Standard',
                'reference_number' => $serviceRequest->reference_number,
                'current_step' => "Step {$step}",
                'progress' => "{$progress}%",
                'uploaded_docs_count' => $uploadedCount,
                'last_saved' => now()->format('M d, Y h:i A'),
                'notes' => "Client {$serviceRequest->client_name} started application {$serviceRequest->reference_number} ({$serviceRequest->service_name}) and currently stopped at Step {$step}.",
            ], $serviceRequest);

            $serviceRequest->updateQuietly(['admin_notified_draft_at' => now()]);
        }

        return response()->json([
            'status' => 'success',
            'last_saved' => now()->format('h:i:s A'),
            'progress' => $progress,
        ]);
    }

    // Immediate Document Upload Handler (Requirement #30)
    public function uploadDocument(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'document_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:10240'],
            'document_name' => ['required', 'string', 'max:255'],
        ]);

        $file = $request->file('document_file');
        $docName = $request->input('document_name');

        $path = \App\Helpers\FileUploadHelper::store($file, 'requests');
        $originalName = $file->getClientOriginalName();

        $doc = RequestDocument::create([
            'service_request_id' => $serviceRequest->id,
            'document_name' => $docName,
            'file_path' => '/storage/' . $path,
            'file_name' => $originalName,
            'status' => 'Received',
        ]);

        $serviceRequest->update(['last_saved_at' => now()]);

        return response()->json([
            'status' => 'success',
            'document' => [
                'id' => $doc->id,
                'document_name' => $doc->document_name,
                'file_name' => $doc->file_name,
                'file_path' => app_file_url($doc->file_path),
                'status' => $doc->status,
            ],
        ]);
    }

    public function deleteDocument(Request $request, RequestDocument $document)
    {
        if ($document->serviceRequest->client_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
        }

        if (!empty($document->file_path)) {
            $relPath = str_replace('/storage/', '', $document->file_path);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($relPath);
        }

        $document->delete();

        return response()->json(['status' => 'success']);
    }

    // Final Application Submission Handler (Requirement #14, #15, #27)
    public function submitApplication(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $service = $serviceRequest->service;
        $initialStage = $service->workflowStages()->orderBy('sort_order', 'asc')->first();

        $serviceRequest->update([
            'status' => 'Awaiting Assignment',
            'current_stage_id' => $initialStage?->id,
            'current_stage_name' => $initialStage?->stage_name ?? 'Awaiting Assignment',
            'submitted_at' => now(),
            'last_saved_at' => now(),
            'current_step' => 4,
            'progress_percent' => 100,
        ]);

        // Record Initial Stage History
        \App\Models\ApplicationStageHistory::create([
            'service_request_id' => $serviceRequest->id,
            'stage_id' => $initialStage?->id,
            'stage_name' => $initialStage?->stage_name ?? 'Application Submitted',
            'status' => 'Awaiting Assignment',
            'changed_by_user_id' => Auth::id(),
            'notes' => 'Application submitted by applicant. Awaiting admin staff assignment.',
            'is_user_visible' => true,
        ]);

        // Audit Log
        \App\Services\AuditLogger::log('application_submitted', 'ServiceRequest', (string) $serviceRequest->id, $serviceRequest->reference_number, null, [
            'service_name' => $serviceRequest->service_name,
            'client_name' => $serviceRequest->client_name,
        ]);

        // Send Email Notifications (Requirement #14 & #15)
        $client = Auth::user();
        \App\Services\EmailNotificationService::send('new_application_user', $client->email, [
            'full_name' => $client->name,
            'service_name' => $serviceRequest->service_name,
            'reference_number' => $serviceRequest->reference_number,
            'status' => 'Awaiting Assignment',
        ], $serviceRequest, $client);

        $adminEmail = config('mail.from.address', 'admin@dootor-enterprises.com');
        \App\Services\EmailNotificationService::send('new_application_admin', $adminEmail, [
            'full_name' => $client->name,
            'service_name' => $serviceRequest->service_name,
            'reference_number' => $serviceRequest->reference_number,
            'country_applying_from' => $serviceRequest->country_applying_from,
            'country_service_requested' => $serviceRequest->country_service_requested,
            'application_date' => now()->format('M d, Y h:i A'),
        ], $serviceRequest);

        return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Application ' . $serviceRequest->reference_number . ' submitted successfully!');
    }

    // Method B: Printable Form Download
    public function downloadForm(\App\Models\Service $service)
    {
        $data = \App\Services\PrintableFormGenerator::getFormData($service, Auth::user());
        return view('services.printable-form', $data);
    }

    // Method B: Manual Completed Form Upload
    public function submitManual(Request $request, \App\Models\Service $service)
    {
        $data = $request->validate([
            'manual_form' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:20480'],
            'passport_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'documents.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $client = Auth::user();
        $totalPrice = (float) ($service->price + $service->service_fee + $service->processing_fee);
        $admin = User::where('role', 'admin')->first() ?? $client;

        $manualFormPath = null;
        if ($request->hasFile('manual_form')) {
            $mPath = \App\Helpers\FileUploadHelper::store($request->file('manual_form'), 'manual_forms');
            $manualFormPath = '/storage/' . $mPath;
        }

        $passportPath = null;
        if ($request->hasFile('passport_photo')) {
            $pPath = \App\Helpers\FileUploadHelper::store($request->file('passport_photo'), 'passports');
            $passportPath = '/storage/' . $pPath;
        }

        $referenceNumber = \App\Services\ReferenceNumberGenerator::generate($service->id);
        $initialStage = $service->workflowStages()->orderBy('sort_order', 'asc')->first();

        $serviceRequest = ServiceRequest::create([
            'reference_number' => $referenceNumber,
            'application_method' => 'manual',
            'service_id' => $service->id,
            'vendor_service_id' => null,
            'service_name' => $service->name,
            'price' => $totalPrice,
            'amount_paid' => 0,
            'outstanding_balance' => $totalPrice,
            'vendor_id' => $admin->id,
            'vendor_name' => $admin->name ?? 'Dooter Enterprises Admin',
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_email' => $client->email,
            'status' => 'Application Submitted',
            'current_stage_id' => $initialStage?->id,
            'current_stage_name' => $initialStage?->stage_name ?? 'Application Submitted',
            'payment_status' => 'Unpaid',
            'manual_form_path' => $manualFormPath,
            'passport_photo_path' => $passportPath,
        ]);

        // Record Completed Manual Application Form as Document
        RequestDocument::create([
            'service_request_id' => $serviceRequest->id,
            'document_name' => 'Completed Official Application Form (Manual Upload)',
            'file_path' => $manualFormPath,
            'file_name' => $request->file('manual_form')->getClientOriginalName(),
            'status' => 'Received',
        ]);

        // Record Initial Stage History
        \App\Models\ApplicationStageHistory::create([
            'service_request_id' => $serviceRequest->id,
            'stage_id' => $initialStage?->id,
            'stage_name' => $initialStage?->stage_name ?? 'Application Submitted',
            'status' => 'Application Submitted',
            'changed_by_user_id' => $client->id,
            'notes' => 'Manual completed form uploaded successfully.',
            'is_user_visible' => true,
        ]);

        // Supporting Documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $docName => $file) {
                $path = \App\Helpers\FileUploadHelper::store($file, 'requests');
                $originalName = is_object($file) ? $file->getClientOriginalName() : 'Document';
                $displayName = is_string($docName) ? ucwords(str_replace('_', ' ', $docName)) : $originalName;

                RequestDocument::create([
                    'service_request_id' => $serviceRequest->id,
                    'document_name' => $displayName,
                    'file_path' => '/storage/' . $path,
                    'file_name' => $originalName,
                    'status' => 'Received',
                ]);
            }
        }

        // Send Email Notifications
        \App\Services\EmailNotificationService::send('new_application_user', $client->email, [
            'full_name' => $client->name,
            'service_name' => $service->name,
            'reference_number' => $referenceNumber,
            'status' => 'Application Submitted',
        ], $serviceRequest, $client);

        \App\Services\EmailNotificationService::send('new_application_admin', config('mail.from.address', 'support@dootor-enterprises.com'), [
            'full_name' => $client->name,
            'service_name' => $service->name,
            'reference_number' => $referenceNumber,
        ], $serviceRequest);

        return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Manual Form Application ' . $serviceRequest->reference_number . ' submitted successfully!');
    }

    public function payRequest(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'payment_reference' => ['required', 'string', 'max:255'],
            'payment_gateway' => ['required', Rule::in(['paystack', 'credo'])],
        ]);

        $serviceRequest->update([
            'payment_reference' => $data['payment_reference'],
            'payment_gateway' => $data['payment_gateway'],
            'status' => 'Payment Confirmed',
            'payment_status' => 'Paid',
            'amount_paid' => $serviceRequest->price,
            'outstanding_balance' => 0,
        ]);
        $serviceRequest->syncStatusToWorkflowStage('Payment Confirmed', 'Payment reference submitted and verified.');

        return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Payment verified! Application status updated to Payment Confirmed.');
    }

    public function deleteRequest(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }
        if ($serviceRequest->payment_status === 'Paid') {
            return redirect()->back()->with('error', 'Paid requests cannot be deleted.');
        }

        $serviceRequest->delete();

        return redirect()->route('client.requests')->with('success', 'Request deleted successfully.');
    }

    public function settings()
    {
        return view('client.settings', [
            'user' => Auth::user(),
            'allCountries' => \App\Services\LocationService::allCountries(),
            'defaultCountry' => 'Canada',
        ]);
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country_applying_from' => ['nullable', 'string', 'max:255'],
            'country_service_requested' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = \App\Helpers\FileUploadHelper::store($request->file('avatar'), 'uploads');
            $data['avatar_url'] = '/storage/' . $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile settings updated successfully.');
    }
}
