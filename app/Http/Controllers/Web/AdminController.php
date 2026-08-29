<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $vendors = User::where('role', 'vendor')->latest()->get();
        $pendingVendors = $vendors->where('status', 'Pending')->values();

        return view('admin.dashboard', [
            'total_vendors' => $vendors->count(),
            'pending_approvals' => $pendingVendors->count(),
            'total_users' => User::count(),
            'total_requests' => ServiceRequest::count(),
            'pending_vendors' => $pendingVendors->load('kycProfile')->take(5),
        ]);
    }

    public function approvals()
    {
        $pendingVendors = User::where('role', 'vendor')
            ->where('status', 'Pending')
            ->with('kycProfile')
            ->latest()
            ->get();

        return view('admin.approvals', [
            'pending_vendors' => $pendingVendors,
        ]);
    }

    public function approveVendor(Request $request, User $vendor)
    {
        $vendor->update(['status' => 'Approved']);
        if ($vendor->kycProfile) {
            $vendor->kycProfile->update(['status' => 'Approved']);
        }

        return redirect()->back()->with('success', 'Vendor has been approved successfully.');
    }

    public function rejectVendor(Request $request, User $vendor)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $vendor->update(['status' => 'Rejected']);
        if ($vendor->kycProfile) {
            $vendor->kycProfile->update([
                'status' => 'Rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);
        }

        return redirect()->back()->with('success', 'Vendor application rejected.');
    }

    public function users(Request $request)
    {
        $query = User::query()->with(['kycProfile', 'storefrontSetting'])->latest();

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users', [
            'users' => $users,
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['sometimes', Rule::in(['Approved', 'Pending', 'Rejected', 'Disabled'])],
            'role' => ['sometimes', Rule::in(['admin', 'vendor', 'client'])],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($data);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function services()
    {
        $services = Service::latest()->get();
        return view('admin.services', [
            'services' => $services,
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'status' => ['sometimes', Rule::in(['Active', 'Inactive'])],
        ]);

        Service::create($data);

        return redirect()->back()->with('success', 'Service created successfully.');
    }

    public function updateService(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'status' => ['sometimes', Rule::in(['Active', 'Inactive'])],
        ]);

        $service->update($data);

        return redirect()->back()->with('success', 'Service updated successfully.');
    }

    public function deleteService(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted successfully.');
    }

    public function settings()
    {
        $settings = SystemSetting::firstOrCreate([]);
        return view('admin.settings', [
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'platform_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'brand_template' => ['nullable', Rule::in(['classic', 'editorial', 'compact', 'showcase'])],
            'brand_theme' => ['nullable', Rule::in(['light', 'dark'])],
            'brand_color_palette' => ['nullable', Rule::in(['default', 'blue', 'green', 'purple', 'red', 'orange', 'yellow', 'teal'])],
            'brand_primary_color' => ['nullable', 'string', 'max:20'],
            'brand_secondary_color' => ['nullable', 'string', 'max:20'],
            'brand_gradient_from' => ['nullable', 'string', 'max:20'],
            'brand_gradient_to' => ['nullable', 'string', 'max:20'],
            'default_currency' => ['nullable', 'string', 'max:10'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'payment_gateway' => ['nullable', Rule::in(['paystack', 'credo'])],
            'payments_enabled' => ['nullable', 'boolean'],
            'payment_mode' => ['nullable', Rule::in(['test', 'live'])],
            'paystack_public_key' => ['nullable', 'string'],
            'paystack_secret_key' => ['nullable', 'string'],
            'paystack_base_url' => ['nullable', 'string'],
            'credo_public_key' => ['nullable', 'string'],
            'credo_secret_key' => ['nullable', 'string'],
            'credo_base_url' => ['nullable', 'string'],
        ]);

        $settings = SystemSetting::firstOrCreate([]);
        $settings->update($data);

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }
}
