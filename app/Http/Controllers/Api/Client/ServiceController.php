<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\VendorService;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'services' => VendorService::with('vendor.storefrontSetting')
                ->where('status', 'Active')
                ->latest()
                ->get(),
        ]);
    }
}
