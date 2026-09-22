<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected string $publicKey;
    protected string $paymentMode;

    public function __construct()
    {
        $setting = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $setting = SystemSetting::first();
            }
        } catch (\Throwable $e) {
            // Fallback to env/config if table unavailable
        }

        $this->secretKey = trim((string) ($setting->paystack_secret_key ?? (config('services.paystack.secret_key') ?: env('PAYSTACK_SECRET_KEY', ''))));
        $this->publicKey = trim((string) ($setting->paystack_public_key ?? (config('services.paystack.public_key') ?: env('PAYSTACK_PUBLIC_KEY', ''))));
        $this->baseUrl = trim((string) ($setting->paystack_base_url ?? (config('services.paystack.base_url') ?: env('PAYSTACK_BASE_URL', 'https://api.paystack.co'))));
        $this->paymentMode = trim((string) ($setting->payment_mode ?? (config('services.paystack.mode') ?: env('PAYSTACK_PAYMENT_MODE', env('PAYMENT_MODE', 'live')))));
    }

    /**
     * Initialize a Paystack payment transaction.
     */
    public function initializePayment(array $params): array
    {
        $rawAmount = (float) ($params['amount'] ?? 0);
        $amountInMinorUnits = (int) round($rawAmount * 100);

        $envCallback = config('services.paystack.callback_url') ?: env('PAYSTACK_CALLBACK_URL');
        $callbackUrl = !empty($params['callback_url']) ? $params['callback_url'] : ($envCallback ?: route('payment.paystack.callback'));
        $cleanCallbackUrl = str_replace(' ', '%20', $callbackUrl);

        if (str_contains(strtolower($cleanCallbackUrl), 'localhost')) {
            $cleanCallbackUrl = $envCallback ?: route('payment.paystack.callback');
        }

        $currency = strtoupper($params['currency'] ?? (config('services.payment.currency') ?: env('PORTAL_BASE_CURRENCY', 'NGN')));

        $payload = [
            'amount' => $amountInMinorUnits,
            'email' => $params['email'] ?? '',
            'currency' => $currency,
            'reference' => $params['reference'] ?? ('DOOTOR-' . time() . '-' . rand(1000, 9999)),
            'callback_url' => $cleanCallbackUrl,
        ];

        if (!empty($params['metadata'])) {
            $payload['metadata'] = (object) $params['metadata'];
        }

        if (empty($this->secretKey)) {
            Log::error('Paystack Initialization Failed: Secret Key not configured.');
            return [
                'status' => false,
                'message' => 'Paystack API Secret Key is missing. Please set your Paystack Secret Key in Admin System Settings or .env file.',
            ];
        }

        $url = rtrim($this->baseUrl, '/') . '/transaction/initialize';

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $resData = $response->json();
                if (!empty($resData['status']) && !empty($resData['data']['authorization_url'])) {
                    return [
                        'status' => true,
                        'data' => [
                            'authorizationUrl' => $resData['data']['authorization_url'],
                            'accessCode' => $resData['data']['access_code'] ?? null,
                            'reference' => $resData['data']['reference'] ?? $payload['reference'],
                        ],
                        'raw' => $resData,
                    ];
                }
            }

            $msg = $response->json('message') ?? $response->body();
            Log::error('Paystack Initialization Error', ['status' => $response->status(), 'body' => $msg]);

            return [
                'status' => false,
                'message' => 'Paystack API Error: ' . ($msg ?: 'Unable to initialize payment session.'),
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack Initialization Exception', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Paystack Connection Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a transaction via reference directly against Paystack API.
     */
    public function verifyTransaction(string $reference): array
    {
        $reference = trim($reference);
        if (empty($reference)) {
            return [
                'status' => false,
                'message' => 'Empty transaction reference provided.',
            ];
        }

        if (empty($this->secretKey)) {
            return [
                'status' => false,
                'message' => 'Paystack API Secret Key is missing.',
            ];
        }

        $url = rtrim($this->baseUrl, '/') . '/transaction/verify/' . urlencode($reference);

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $isSuccessful = (!empty($data['status']) && isset($data['data']['status']) && strtolower($data['data']['status']) === 'success');

                return [
                    'status' => true,
                    'is_successful' => $isSuccessful,
                    'data' => $data,
                    'message' => $data['message'] ?? ($isSuccessful ? 'Transaction verified successfully.' : 'Transaction verification failed.'),
                ];
            }

            $msg = $response->json('message') ?? $response->body();
            return [
                'status' => false,
                'is_successful' => false,
                'message' => 'Paystack Verification Failed: ' . $msg,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'is_successful' => false,
                'message' => 'Paystack Verification Exception: ' . $e->getMessage(),
            ];
        }
    }
}
