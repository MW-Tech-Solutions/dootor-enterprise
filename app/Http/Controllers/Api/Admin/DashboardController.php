<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $vendors = User::where('role', 'vendor')->latest()->get();
        $pendingVendors = $vendors->where('status', 'Pending')->values();

        return response()->json([
            'total_vendors' => $vendors->count(),
            'pending_approvals' => $pendingVendors->count(),
            'total_users' => User::count(),
            'total_requests' => ServiceRequest::count(),
            'pending_vendors' => $pendingVendors->load('kycProfile')->take(5)->values(),
        ]);
    }
}
