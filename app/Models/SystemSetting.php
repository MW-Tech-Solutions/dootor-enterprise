<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
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
        'paystack_secret_key',
        'paystack_base_url',
        'credo_public_key',
        'credo_secret_key',
        'credo_base_url',
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_pass',
        'smtp_encryption',
        'smtp_sender_name',
        'smtp_sender_email',
        'template_vendor_approval',
        'template_client_registration',
        'template_payment_confirmation',
        'template_service_update',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'payments_enabled' => 'boolean',
            'smtp_port' => 'integer',
        ];
    }
}
