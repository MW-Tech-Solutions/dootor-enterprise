<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'requests' => $request->user()->clientRequests()->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'vendor_service_id' => ['required', 'exists:vendor_services,id'],
            'documents' => ['nullable', 'array'],
        ]);

        $vendorService = VendorService::with('vendor')->findOrFail($data['vendor_service_id']);
        abort_unless($vendorService->status === 'Active', 422, 'This service is not active.');

        $client = $request->user();

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
            'documents' => $data['documents'] ?? [],
            'payment_status' => 'Unpaid',
        ]);

        return response()->json(['request' => $serviceRequest], 201);
    }

    public function show(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        abort_unless($serviceRequest->client_id === $request->user()->id, 403);

        return response()->json(['request' => $serviceRequest]);
    }

    public function updatePayment(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        abort_unless($serviceRequest->client_id === $request->user()->id, 403);

        $data = $request->validate([
            'payment_reference' => ['required', 'string'],
            'payment_gateway' => ['required', Rule::in(['paystack', 'credo'])],
        ]);

        $serviceRequest->update([
            ...$data,
            'status' => 'Pending',
            'payment_status' => 'Paid',
        ]);

        return response()->json(['request' => $serviceRequest->fresh()]);
    }

    public function destroy(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        abort_unless($serviceRequest->client_id === $request->user()->id, 403);
        abort_if($serviceRequest->payment_status === 'Paid', 422, 'Paid requests cannot be deleted.');

        $serviceRequest->delete();

        return response()->json(['message' => 'Request deleted.']);
    }
}
