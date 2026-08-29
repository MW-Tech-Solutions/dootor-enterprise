<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $services = $user->vendorServices()->latest()->get();
        $requests = $user->vendorRequests()->latest()->take(10)->get();

        return response()->json([
            'services' => $services,
            'requests' => $requests,
            'active_services_count' => $services->where('status', 'Active')->count(),
            'total_services_count' => $services->count(),
            'total_requests_count' => $user->vendorRequests()->count(),
        ]);
    }
}
