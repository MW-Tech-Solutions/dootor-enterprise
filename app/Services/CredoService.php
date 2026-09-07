<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CredoService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected string $publicKey;

    public function __construct()
    {
        $setting = \App\Models\SystemSetting::first();
        $this->baseUrl = !empty($setting?->credo_base_url) ? $setting->credo_base_url : env('CREDO_BASE_URL', 'https://api.credocentral.com');
        $this->secretKey = !empty($setting?->credo_secret_key) ? $setting->credo_secret_key : env('CREDO_SECRET_KEY', '');
        $this->publicKey = !empty($setting?->credo_public_key) ? $setting->credo_public_key : env('CREDO_PUBLIC_KEY', '');
    }

    /**
     * Initialize a Credo payment transaction.
     */
    public function initializePayment(array $params): array
    {
        $url = rtrim($this->baseUrl, '/') . '/transaction/initialize';

        // Credo API expects amount in minor currency units (multiplied by 100)
        $rawAmount = (float) ($params['amount'] ?? 0);
        $amountInMinorUnits = (int) round($rawAmount * 100);

        // Determine callback URL dynamically from ENV or parameters
        $envCallback = env('CREDO_CALLBACK_URL') ?: env('CREDO_FRONTEND_CALLBACK_URL');
        $callbackUrl = !empty($params['callback_url']) ? $params['callback_url'] : ($envCallback ?: route('payment.credo.callback'));
        $cleanCallbackUrl = str_replace(' ', '%20', $callbackUrl);

        if (str_contains(strtolower($cleanCallbackUrl), 'localhost')) {
            $cleanCallbackUrl = $envCallback ?: 'https://dootor-enterprise.kisprojectslab.com/payment/credo/callback';
        }

        $payload = [
            'amount' => $amountInMinorUnits,
            'currency' => $params['currency'] ?? env('PORTAL_BASE_CURRENCY', 'USD'),
            'email' => $params['email'] ?? '',
            'callbackUrl' => $cleanCallbackUrl,
            'reference' => $params['reference'] ?? ('CREDO-' . time() . '-' . rand(1000, 9999)),
            'metadata' => $params['metadata'] ?? [],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Credo Payment Initialization Failed', ['response' => $response->body()]);
            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Payment initialization failed: ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Credo Payment Exception', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a transaction via reference directly against Credo Central API.
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

        // Try primary Credo verification endpoint: GET /transaction/{transRef}/verify
        $url = rtrim($this->baseUrl, '/') . '/transaction/' . urlencode($reference) . '/verify';

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->secretKey,
                'Accept' => 'application/json',
            ])->get($url);

            // Fallback endpoint if 404: GET /transaction/verify/{transRef}
            if ($response->status() === 404) {
                $altUrl = rtrim($this->baseUrl, '/') . '/transaction/verify/' . urlencode($reference);
                $response = Http::withHeaders([
                    'Authorization' => $this->secretKey,
                    'Accept' => 'application/json',
                ])->get($altUrl);
            }

            if ($response->successful()) {
                $data = $response->json();
                
                $statusCode = (string) ($data['status'] ?? $data['statusCode'] ?? $data['data']['status'] ?? '');
                $txStatus = strtolower((string) ($data['data']['status'] ?? $data['status'] ?? ''));
                
                $isSuccessful = in_array($statusCode, ['00', '200', '0']) 
                    || $txStatus === 'successful' 
                    || $txStatus === 'success' 
                    || $txStatus === 'paid'
                    || !empty($data['data']['businessAmount']);

                return [
                    'status' => true,
                    'data' => $data,
                    'is_successful' => $isSuccessful,
                    'message' => $data['message'] ?? 'Transaction details fetched successfully.',
                ];
            }

            return [
                'status' => false,
                'message' => $response->json('message') ?? ('Credo API error HTTP ' . $response->status()),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
