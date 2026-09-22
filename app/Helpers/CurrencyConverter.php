<?php

namespace App\Helpers;

use App\Models\Service;
use App\Models\VendorService;
use Illuminate\Support\Facades\Log;

class CurrencyConverter
{
    /**
     * Convert catalog services and vendor services when default currency changes.
     * Fixed Exchange Rate: 1 USD = 1500 NGN
     */
    public static function convertPrices(string $oldCurrency, string $newCurrency, float $exchangeRate = 1500.0): void
    {
        $oldCurrency = strtoupper(trim($oldCurrency));
        $newCurrency = strtoupper(trim($newCurrency));

        if (empty($oldCurrency) || empty($newCurrency) || $oldCurrency === $newCurrency) {
            return;
        }

        try {
            if ($oldCurrency === 'USD' && $newCurrency === 'NGN') {
                // USD -> NGN (Multiply by 1500)
                foreach (Service::all() as $svc) {
                    $svc->update([
                        'price' => round((float) $svc->price * $exchangeRate, 2),
                        'service_fee' => round((float) $svc->service_fee * $exchangeRate, 2),
                        'processing_fee' => round((float) $svc->processing_fee * $exchangeRate, 2),
                    ]);
                }
                foreach (VendorService::all() as $vSvc) {
                    $vSvc->update([
                        'price' => round((float) $vSvc->price * $exchangeRate, 2),
                    ]);
                }
                Log::info("Converted service prices from USD to NGN at rate 1 USD = {$exchangeRate} NGN.");
            } elseif ($oldCurrency === 'NGN' && $newCurrency === 'USD') {
                // NGN -> USD (Divide by 1500)
                foreach (Service::all() as $svc) {
                    $svc->update([
                        'price' => round((float) $svc->price / $exchangeRate, 2),
                        'service_fee' => round((float) $svc->service_fee / $exchangeRate, 2),
                        'processing_fee' => round((float) $svc->processing_fee / $exchangeRate, 2),
                    ]);
                }
                foreach (VendorService::all() as $vSvc) {
                    $vSvc->update([
                        'price' => round((float) $vSvc->price / $exchangeRate, 2),
                    ]);
                }
                Log::info("Converted service prices from NGN to USD at rate 1 USD = {$exchangeRate} NGN.");
            }
        } catch (\Throwable $e) {
            Log::error('Currency conversion error: ' . $e->getMessage());
        }
    }
}
