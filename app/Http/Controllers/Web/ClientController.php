<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RequestDocument;
use App\Models\ServiceRequest;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $requests = $user->clientRequests()->latest()->take(10)->get();

        return view('client.dashboard', [
            'requests' => $requests,
            'total_requests_count' => $user->clientRequests()->count(),
            'paid_requests_count' => $user->clientRequests()->where('payment_status', 'Paid')->count(),
            'unpaid_requests_count' => $user->clientRequests()->where('payment_status', 'Unpaid')->count(),
        ]);
    }

    public function services()
    {
        $services = \App\Models\Service::where('status', 'Active')->latest()->get();
        return view('client.services', ['services' => $services]);
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

        $service->load(['fields' => function ($q) {
            $q->where('is_enabled', true)->orderBy('sort_order', 'asc');
        }]);

        return view('client.book', ['service' => $service]);
    }

    // Method A: Online Application Submission
    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'form_data' => ['nullable', 'array'],
            'passport_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'documents.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $service = \App\Models\Service::findOrFail($data['service_id']);
        abort_unless($service->status === 'Active', 422, 'This service is not active.');

        $client = Auth::user();
        $totalPrice = (float) ($service->price + $service->service_fee + $service->processing_fee);
        $admin = User::where('role', 'admin')->first() ?? $client;

        $passportPath = null;
        if ($request->hasFile('passport_photo')) {
            $pPath = \App\Helpers\FileUploadHelper::store($request->file('passport_photo'), 'passports');
            $passportPath = '/storage/' . $pPath;
        }

        $referenceNumber = \App\Services\ReferenceNumberGenerator::generate($service->id);

        $initialStage = $service->workflowStages()->orderBy('sort_order', 'asc')->first();

        $serviceRequest = ServiceRequest::create([
            'reference_number' => $referenceNumber,
            'application_method' => 'online',
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
            'form_data' => $request->input('form_data', []),
            'passport_photo_path' => $passportPath,
        ]);

        // Record Initial Stage History
        \App\Models\ApplicationStageHistory::create([
            'service_request_id' => $serviceRequest->id,
            'stage_id' => $initialStage?->id,
            'stage_name' => $initialStage?->stage_name ?? 'Application Submitted',
            'status' => 'Application Submitted',
            'changed_by_user_id' => $client->id,
            'notes' => 'Online application submitted successfully.',
            'is_user_visible' => true,
        ]);

        // Process uploaded supporting documents
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

        return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Online Application ' . $serviceRequest->reference_number . ' submitted successfully!');
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
            'africanCountries' => \App\Services\AfricanLocationService::allCountries(),
            'defaultCountry' => 'Nigeria',
        ]);
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', Rule::in(\App\Services\AfricanLocationService::countryNames())],
            'state' => ['nullable', 'string', 'max:255'],
        ]);

        if (!empty($data['state']) && !\App\Services\AfricanLocationService::isValidPair($data['country'], $data['state'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'state' => ["The selected state/region does not belong to {$data['country']}."],
            ]);
        }

        if ($request->hasFile('avatar')) {
            $path = \App\Helpers\FileUploadHelper::store($request->file('avatar'), 'uploads');
            $data['avatar_url'] = '/storage/' . $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile settings updated successfully.');
    }
}
