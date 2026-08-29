<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function show(Request $request, User $vendor, VendorService $vendorService): JsonResponse
    {
        abort_unless($vendor->role === 'vendor' && $vendorService->vendor_id === $vendor->id, 404);

        return response()->json([
            'current_user' => $request->user(),
            'vendor' => $vendor,
            'service' => $vendorService,
            'settings' => SystemSetting::first(),
        ]);
    }

    public function retry(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        abort_unless($serviceRequest->client_id === $request->user()->id, 403);

        return response()->json([
            'current_user' => $request->user(),
            'vendor' => $serviceRequest->vendor,
            'service' => $serviceRequest->vendorService,
            'request' => $serviceRequest,
            'settings' => SystemSetting::first(),
        ]);
    }
}
