<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'services' => $request->user()->vendorServices()->latest()->get(),
        ]);
    }

    public function platformServices(Request $request): JsonResponse
    {
        return response()->json([
            'services' => Service::where('status', 'Active')->latest()->get(),
            'my_service_ids' => $request->user()->vendorServices()->pluck('service_id'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'image_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $service = Service::findOrFail($data['service_id']);

        $vendorService = $request->user()->vendorServices()->create([
            'service_id' => $service->id,
            'name' => $service->name,
            'price' => $data['price'],
            'description' => $data['description'],
            'image_url' => $data['image_url'] ?? $service->image_url,
            'status' => 'Active',
        ]);

        return response()->json(['service' => $vendorService], 201);
    }

    public function update(Request $request, VendorService $vendorService): JsonResponse
    {
        abort_unless($vendorService->vendor_id === $request->user()->id, 403);

        $vendorService->update($request->validate([
            'price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['sometimes', 'string'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'status' => ['sometimes', Rule::in(['Active', 'Inactive'])],
        ]));

        return response()->json(['service' => $vendorService->fresh()]);
    }

    public function destroy(Request $request, VendorService $vendorService): JsonResponse
    {
        abort_unless($vendorService->vendor_id === $request->user()->id, 403);

        $vendorService->delete();

        return response()->json(['message' => 'Vendor service deleted.']);
    }
}
