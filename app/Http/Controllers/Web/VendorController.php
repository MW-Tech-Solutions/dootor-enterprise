<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArchivedClient;
use App\Models\KycProfile;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\StorefrontSetting;
use App\Models\User;
use App\Models\VendorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $services = $user->vendorServices()->latest()->get();
        $requests = $user->vendorRequests()->latest()->take(10)->get();

        return view('vendor.dashboard', [
            'services' => $services,
            'requests' => $requests,
            'active_services_count' => $services->where('status', 'Active')->count(),
            'total_services_count' => $services->count(),
            'total_requests_count' => $user->vendorRequests()->count(),
        ]);
    }

    public function kyc()
    {
        $user = Auth::user();
        $kyc = $user->kycProfile()->firstOrCreate([], ['status' => 'NotStarted']);
        return view('vendor.kyc', ['kyc' => $kyc]);
    }

    public function storeKyc(Request $request)
    {
        $user = Auth::user();
        $kyc = $user->kycProfile()->firstOrCreate([], ['status' => 'NotStarted']);

        $rules = [
            'nin' => ['required', 'string', 'size:11'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'terms_agreed' => ['accepted'],
            'privacy_agreed' => ['accepted'],
            'data_consent_agreed' => ['accepted'],
            'digital_signature' => ['required', 'string', 'max:255'],
        ];

        // File uploads are required on first submission, otherwise optional if already exists
        if (!$kyc->id_card_url) {
            $rules['id_card'] = ['required', 'file', 'image', 'max:2048'];
        }
        if (!$kyc->proof_of_address_url) {
            $rules['proof_of_address'] = ['required', 'file', 'image', 'max:2048'];
        }
        if (!$kyc->personal_image_url) {
            $rules['personal_image'] = ['required', 'file', 'image', 'max:2048'];
        }

        $data = $request->validate($rules);

        $updateData = [
            'nin' => $data['nin'],
            'bank_name' => $data['bank_name'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
            'terms_agreed' => true,
            'privacy_agreed' => true,
            'data_consent_agreed' => true,
            'digital_signature' => $data['digital_signature'],
            'status' => 'Submitted',
            'rejection_reason' => null,
        ];

        if ($request->hasFile('id_card')) {
            $path = $request->file('id_card')->store('uploads', 'public');
            $updateData['id_card_url'] = '/storage/' . $path;
        }
        if ($request->hasFile('proof_of_address')) {
            $path = $request->file('proof_of_address')->store('uploads', 'public');
            $updateData['proof_of_address_url'] = '/storage/' . $path;
        }
        if ($request->hasFile('personal_image')) {
            $path = $request->file('personal_image')->store('uploads', 'public');
            $updateData['personal_image_url'] = '/storage/' . $path;
        }

        $kyc->update($updateData);
        $user->update(['status' => 'Pending']);

        return redirect()->route('vendor.kyc')->with('success', 'KYC application submitted successfully! Pending administrator approval.');
    }

    public function myServices()
    {
        $user = Auth::user();
        $vendorServices = $user->vendorServices()->with('service')->latest()->get();
        return view('vendor.my-services', ['vendorServices' => $vendorServices]);
    }

    public function services()
    {
        $user = Auth::user();
        $enabledServiceIds = $user->vendorServices()->pluck('service_id')->toArray();
        $availableServices = Service::where('status', 'Active')
            ->whereNotIn('id', $enabledServiceIds)
            ->get();

        return view('vendor.services', ['availableServices' => $availableServices]);
    }

    public function enableService(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
        ]);

        $user = Auth::user();

        VendorService::create([
            'vendor_id' => $user->id,
            'service_id' => $data['service_id'],
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'status' => 'Active',
        ]);

        return redirect()->route('vendor.my-services')->with('success', 'Service enabled successfully.');
    }

    public function updateService(Request $request, VendorService $vendorService)
    {
        if ($vendorService->vendor_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $vendorService->update($data);

        return redirect()->back()->with('success', 'Service updated successfully.');
    }

    public function deleteService(VendorService $vendorService)
    {
        if ($vendorService->vendor_id !== Auth::id()) {
            abort(403);
        }

        $vendorService->delete();

        return redirect()->back()->with('success', 'Service removed.');
    }

    public function clients()
    {
        $user = Auth::user();
        
        // Active clients are users who have made requests to this vendor
        $clientIds = ServiceRequest::where('vendor_id', $user->id)
            ->pluck('client_id')
            ->unique();

        $clients = User::whereIn('id', $clientIds)->latest()->get();
        $archivedClients = ArchivedClient::where('vendor_id', $user->id)->latest()->get();

        return view('vendor.clients', [
            'clients' => $clients,
            'archivedClients' => $archivedClients,
        ]);
    }

    public function archiveClient(Request $request, User $client)
    {
        $user = Auth::user();

        ArchivedClient::firstOrCreate([
            'vendor_id' => $user->id,
            'client_id' => $client->id,
        ], [
            'client_name' => $client->first_name . ' ' . $client->last_name,
            'client_email' => $client->email,
        ]);

        return redirect()->back()->with('success', 'Client archived.');
    }

    public function requests()
    {
        $user = Auth::user();
        $requests = $user->vendorRequests()->with('client')->latest()->get();
        return view('vendor.requests', ['requests' => $requests]);
    }

    public function updateRequestStatus(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->vendor_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(['Awaiting Payment', 'Pending Documents', 'Under Review', 'In Progress', 'Completed', 'Rejected'])],
        ]);

        $serviceRequest->update($data);

        return redirect()->back()->with('success', 'Request status updated successfully.');
    }

    public function settings()
    {
        $user = Auth::user();
        $storefront = $user->storefrontSetting()->firstOrCreate([], [
            'storefront_name' => $user->first_name . ' Hub',
            'theme' => 'light',
            'color_palette' => 'default',
        ]);

        return view('vendor.settings', [
            'storefront' => $storefront,
            'user' => $user,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $storefront = $user->storefrontSetting()->firstOrCreate([]);

        $data = $request->validate([
            'storefront_name' => ['required', 'string', 'max:255'],
            'storefront_about' => ['nullable', 'string'],
            'theme' => ['required', Rule::in(['light', 'dark'])],
            'color_palette' => ['required', Rule::in(['default', 'blue', 'green', 'purple', 'red', 'orange', 'yellow', 'teal'])],
            'gradient' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('uploads', 'public');
            $data['background_image_url'] = '/storage/' . $path;
        }

        $storefront->update($data);

        return redirect()->back()->with('success', 'Storefront settings updated successfully.');
    }

    public function updateProfile(Request $request)
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

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
