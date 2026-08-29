<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VendorService;
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
        // Lists all active custom vendor services across approved vendors
        $services = VendorService::with(['vendor.storefrontSetting', 'service'])
            ->where('status', 'Active')
            ->whereHas('vendor', function ($query) {
                $query->where('status', 'Approved');
            })
            ->latest()
            ->get();

        return view('client.services', ['services' => $services]);
    }

    public function requests()
    {
        $user = Auth::user();
        $requests = $user->clientRequests()->latest()->get();
        return view('client.requests', ['requests' => $requests]);
    }

    public function requestDetails(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        return view('client.request-details', ['request' => $serviceRequest]);
    }

    public function book(VendorService $vendorService)
    {
        if ($vendorService->status !== 'Active') {
            abort(404, 'Service is not active');
        }

        return view('client.book', ['vendorService' => $vendorService]);
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'vendor_service_id' => ['required', 'exists:vendor_services,id'],
            'documents.*' => ['nullable', 'file', 'max:5120'], // Max 5MB per document
        ]);

        $vendorService = VendorService::with('vendor')->findOrFail($data['vendor_service_id']);
        abort_unless($vendorService->status === 'Active', 422, 'This service is not active.');

        $client = Auth::user();

        // Handle file uploads if present
        $uploadedDocs = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('requests', 'public');
                $uploadedDocs[] = [
                    'name' => $file->getClientOriginalName(),
                    'url' => '/storage/' . $path,
                ];
            }
        }

        $serviceRequest = ServiceRequest::create([
            'service_id' => $vendorService->service_id,
            'vendor_service_id' => $vendorService->id,
            'service_name' => $vendorService->name,
            'price' => $vendorService->price,
            'vendor_id' => $vendorService->vendor_id,
            'vendor_name' => $vendorService->vendor->name,
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_email' => $client->email,
            'status' => 'Awaiting Payment',
            'documents' => $uploadedDocs,
            'payment_status' => 'Unpaid',
        ]);

        return redirect()->route('client.requests')->with('success', 'Booking request created successfully. Please complete the payment to start processing.');
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
            'status' => 'Pending',
            'payment_status' => 'Paid',
        ]);

        return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Payment successful! Service is now pending vendor processing.');
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
        return view('client.settings', ['user' => Auth::user()]);
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('uploads', 'public');
            $data['avatar_url'] = '/storage/' . $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile settings updated successfully.');
    }
}
