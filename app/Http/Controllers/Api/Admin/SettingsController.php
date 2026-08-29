<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'settings' => SystemSetting::firstOrCreate([]),
        ]);
    }

    public function update(Request $request): JsonResponse
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
            'smtp_host' => ['nullable', 'string'],
            'smtp_port' => ['nullable', 'integer'],
            'smtp_user' => ['nullable', 'string'],
            'smtp_pass' => ['nullable', 'string'],
            'smtp_encryption' => ['nullable', Rule::in(['none', 'ssl', 'tls'])],
            'smtp_sender_name' => ['nullable', 'string'],
            'smtp_sender_email' => ['nullable', 'email'],
            'template_vendor_approval' => ['nullable', 'string'],
            'template_client_registration' => ['nullable', 'string'],
            'template_payment_confirmation' => ['nullable', 'string'],
            'template_service_update' => ['nullable', 'string'],
        ]);

        $settings = SystemSetting::firstOrCreate([]);
        $settings->update($data);

        return response()->json(['settings' => $settings->fresh()]);
    }
}
