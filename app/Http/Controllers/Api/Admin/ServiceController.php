<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'services' => Service::latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $service = Service::create($this->validated($request));

        return response()->json(['service' => $service], 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $service->update($this->validated($request, partial: true));

        return response()->json(['service' => $service->fresh()]);
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(['message' => 'Service deleted.']);
    }

    private function validated(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'price' => [$required, 'numeric', 'min:0'],
            'description' => [$required, 'string'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'status' => [$partial ? 'sometimes' : 'nullable', Rule::in(['Active', 'Inactive'])],
        ]);
    }
}
