<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'requests' => $request->user()->vendorRequests()->latest()->get(),
        ]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        abort_unless($serviceRequest->vendor_id === $request->user()->id, 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['Awaiting Payment', 'Pending', 'Processing', 'Completed', 'Cancelled'])],
        ]);

        $serviceRequest->update($data);

        return response()->json(['request' => $serviceRequest->fresh()]);
    }
}
