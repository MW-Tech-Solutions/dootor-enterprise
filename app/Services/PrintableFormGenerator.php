<?php

namespace App\Services;

use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\User;

class PrintableFormGenerator
{
    /**
     * Build view parameters for rendering the printable service application form.
     */
    public static function getFormData(Service $service, ?User $user = null): array
    {
        $settings = SystemSetting::first();
        $referenceNumber = ReferenceNumberGenerator::generate($service->id);

        $fields = $service->fields()->where('is_enabled', true)->orderBy('sort_order')->get();

        if ($fields->isEmpty() && !empty($service->custom_fields) && is_array($service->custom_fields)) {
            $syntheticFields = collect();
            foreach ($service->custom_fields as $cf) {
                if (is_string($cf)) {
                    $syntheticFields->push((object)[
                        'field_label' => $cf,
                        'field_type' => 'text',
                        'is_required' => false,
                        'options' => null,
                        'help_text' => null,
                    ]);
                } elseif (is_array($cf) || is_object($cf)) {
                    $cf = (array) $cf;
                    $syntheticFields->push((object)[
                        'field_label' => $cf['field_label'] ?? ($cf['label'] ?? ($cf['name'] ?? 'Information / Detail')),
                        'field_type' => $cf['field_type'] ?? ($cf['type'] ?? 'text'),
                        'is_required' => $cf['is_required'] ?? ($cf['required'] ?? false),
                        'options' => $cf['options'] ?? null,
                        'help_text' => $cf['help_text'] ?? null,
                    ]);
                }
            }
            $fields = $syntheticFields;
        }

        return [
            'settings' => $settings,
            'companyName' => $settings->platform_name ?? 'DOOTOR ENTERPRISES',
            'logoUrl' => !empty($settings->logo_url) ? asset($settings->logo_url) : null,
            'service' => $service,
            'user' => $user,
            'referenceNumber' => $referenceNumber,
            'fields' => $fields,
            'requiredDocuments' => $service->required_documents ?? [],
            'generatedDate' => date('F d, Y'),
        ];
    }
}
