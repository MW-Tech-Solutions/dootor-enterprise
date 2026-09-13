<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CredoService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected string $publicKey;
    protected string $paymentMode;

    public function __construct()
    {
        $this->paymentMode = trim((string) env('CREDO_PAYMENT_MODE', env('PAYMENT_MODE', 'live')));
        $this->baseUrl = trim((string) env('CREDO_BASE_URL', 'https://api.credocentral.com'));
        $this->secretKey = trim((string) env('CREDO_SECRET_KEY', ''));
        $this->publicKey = trim((string) env('CREDO_PUBLIC_KEY', ''));
    }

    /**
     * Initialize a Credo payment transaction.
     */
    public function initializePayment(array $params): array
    {
        // Credo API expects amount in minor currency units (multiplied by 100)
        $rawAmount = (float) ($params['amount'] ?? 0);
        $amountInMinorUnits = (int) round($rawAmount * 100);

        // Determine callback URL dynamically
        $envCallback = env('CREDO_CALLBACK_URL') ?: env('CREDO_FRONTEND_CALLBACK_URL');
        $callbackUrl = !empty($params['callback_url']) ? $params['callback_url'] : ($envCallback ?: route('payment.credo.callback'));
        $cleanCallbackUrl = str_replace(' ', '%20', $callbackUrl);

        if (str_contains(strtolower($cleanCallbackUrl), 'localhost')) {
            $cleanCallbackUrl = $envCallback ?: 'https://dootor-enterprises.com/payment/credo/callback';
        }

        $payload = [
            'amount' => $amountInMinorUnits,
            'currency' => $params['currency'] ?? env('PORTAL_BASE_CURRENCY', 'USD'),
            'email' => $params['email'] ?? '',
            'customerFirstName' => $params['customerFirstName'] ?? $params['first_name'] ?? 'Valued',
            'customerLastName' => $params['customerLastName'] ?? $params['last_name'] ?? 'Customer',
            'phoneNumber' => $params['phoneNumber'] ?? $params['phone'] ?? '08000000000',
            'callbackUrl' => $cleanCallbackUrl,
            'reference' => $params['reference'] ?? ('CREDO-' . time() . '-' . rand(1000, 9999)),
            'metadata' => $params['metadata'] ?? [],
        ];

        // Endpoints & Headers to attempt for resilient payment initialization
        $candidateUrls = array_unique([
            rtrim($this->baseUrl, '/') . '/transaction/initialize',
            'https://api.credocentral.com/transaction/initialize',
            'https://api.sandbox.credocentral.com/transaction/initialize',
        ]);

        $candidateHeaders = [
            'SecretKey' => $this->secretKey,
            'PublicKey' => $this->publicKey,
            'Bearer SecretKey' => 'Bearer ' . $this->secretKey,
            'Bearer PublicKey' => 'Bearer ' . $this->publicKey,
        ];

        $lastError = 'Credo payment initialization failed.';

        foreach ($candidateUrls as $url) {
            foreach ($candidateHeaders as $headerType => $authHeaderValue) {
                if (empty($authHeaderValue) || $authHeaderValue === 'Bearer ') continue;

                try {
                    $response = Http::timeout(10)->withHeaders([
                        'Authorization' => $authHeaderValue,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->post($url, $payload);

                    if ($response->successful()) {
                        $resData = $response->json();
                        if (isset($resData['data']['authorizationUrl']) || (isset($resData['status']) && ($resData['status'] == 200 || $resData['status'] === true))) {
                            return [
                                'status' => true,
                                'data' => $resData,
                            ];
                        }
                    }

                    $msg = $response->json('message') ?? $response->body();
                    $lastError = $msg;
                    
                    if ($response->status() === 400 && !str_contains(strtolower($msg), 'permission')) {
                        break 2;
                    }
                } catch (\Throwable $e) {
                    $lastError = $e->getMessage();
                }
            }
        }

        Log::error('Credo Payment Initialization Failed All Candidates', [
            'error' => $lastError,
            'baseUrl' => $this->baseUrl,
            'hasSecret' => !empty($this->secretKey),
            'hasPublic' => !empty($this->publicKey),
        ]);

        // Clean user-friendly message
        $userFriendlyMessage = $lastError;
        if (str_contains($lastError, 'permissions') || str_contains($lastError, 'credocentral.com')) {
            $userFriendlyMessage = "Credo API Key / Environment Mismatch: The Secret Key or Public Key provided is not recognized for this environment (Live vs Sandbox). Please verify your Credo Secret Key & Public Key in Admin System Settings -> Escrow Payment Gateways.";
        }

        return [
            'status' => false,
            'message' => $userFriendlyMessage,
        ];
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

        $candidateUrls = array_unique([
            rtrim($this->baseUrl, '/') . '/transaction/' . urlencode($reference) . '/verify',
            'https://api.credocentral.com/transaction/' . urlencode($reference) . '/verify',
            'https://api.sandbox.credocentral.com/transaction/' . urlencode($reference) . '/verify',
            rtrim($this->baseUrl, '/') . '/transaction/verify/' . urlencode($reference),
        ]);

        $candidateHeaders = [
            'SecretKey' => $this->secretKey,
            'PublicKey' => $this->publicKey,
        ];

        foreach ($candidateUrls as $url) {
            foreach ($candidateHeaders as $authHeaderValue) {
                if (empty($authHeaderValue)) continue;

                try {
                    $response = Http::timeout(10)->withHeaders([
                        'Authorization' => $authHeaderValue,
                        'Accept' => 'application/json',
                    ])->get($url);

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
                } catch (\Throwable $e) {
                    // Try next candidate
                }
            }
        }

        return [
            'status' => false,
            'message' => 'Unable to verify transaction with Credo Gateway.',
        ];
    }
}
