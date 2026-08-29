<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PublicController extends Controller
{
    public function settings(): JsonResponse
    {
        $settings = SystemSetting::firstOrCreate([], [
            'platform_name' => 'DOOTOR ENTERPRISES',
            'company_name' => 'DOOTOR ENTERPRISES',
            'brand_template' => 'classic',
            'brand_theme' => 'light',
            'brand_color_palette' => 'default',
            'brand_primary_color' => '#004225',
            'brand_secondary_color' => '#d4af37',
            'brand_gradient_from' => '#004225',
            'brand_gradient_to' => '#d4af37',
            'default_currency' => 'USD',
            'payment_gateway' => 'paystack',
            'payments_enabled' => true,
            'payment_mode' => 'test',
        ]);

        return response()->json([
            'settings' => $settings->only([
                'platform_name',
                'company_name',
                'logo_url',
                'brand_template',
                'brand_theme',
                'brand_color_palette',
                'brand_primary_color',
                'brand_secondary_color',
                'brand_gradient_from',
                'brand_gradient_to',
                'default_currency',
                'maintenance_mode',
                'payment_gateway',
                'payments_enabled',
                'payment_mode',
                'paystack_public_key',
                'credo_public_key',
            ]),
        ]);
    }

    public function vendorStorefront(User $vendor): JsonResponse
    {
        abort_unless($vendor->role === 'vendor' && $vendor->status === 'Approved', 404);

        return response()->json([
            'vendor' => $vendor->load('storefrontSetting'),
            'services' => $vendor->vendorServices()
                ->where('status', 'Active')
                ->latest()
                ->get(),
        ]);
    }
}
