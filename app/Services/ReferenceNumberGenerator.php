<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Support\Str;

class ReferenceNumberGenerator
{
    /**
     * Generate a unique, collision-safe reference number.
     * Format: DOOTOR-[SERVICE_CODE]-[YEAR]-[SEQUENCE]
     */
    public static function generate(?int $serviceId = null): string
    {
        $code = 'GEN';
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service && !empty($service->name)) {
                $words = preg_split('/\s+/', strtoupper(trim($service->name)));
                if (count($words) >= 2) {
                    $code = substr($words[0], 0, 2) . substr($words[1], 0, 2);
                } else {
                    $code = substr($words[0], 0, 4);
                }
            }
        }

        $code = preg_replace('/[^A-Z0-9]/', '', $code);
        if (strlen($code) < 3) {
            $code = str_pad($code, 3, 'X', STR_PAD_RIGHT);
        }

        $year = date('Y');

        do {
            $randSeq = str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $referenceNumber = "DOOTOR-{$code}-{$year}-{$randSeq}";
            $exists = ServiceRequest::where('reference_number', $referenceNumber)->exists();
        } while ($exists);

        return $referenceNumber;
    }
}
