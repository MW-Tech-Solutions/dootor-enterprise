<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonerisService
{
    protected string $endpoint;
    protected string $scriptUrl;
    protected string $environment;
    protected string $storeId;
    protected string $apiToken;
    protected string $checkoutId;

    public function __construct()
    {
        $setting = SystemSetting::first();
        $mode = strtolower(trim((string) ($setting?->payment_mode ?: config('services.moneris.mode', 'live'))));
        $isTest = in_array($mode, ['test', 'sandbox', 'qa']);

        $this->environment = $isTest ? 'qa' : 'prod';
        $this->endpoint = $isTest
            ? 'https://gatewayt.moneris.com/chktv2/request/request.php'
            : 'https://gateway.moneris.com/chktv2/request/request.php';
        $this->scriptUrl = $isTest
            ? 'https://gatewayt.moneris.com/chktv2/js/chkt_v1.00.js'
            : 'https://gateway.moneris.com/chktv2/js/chkt_v1.00.js';

        $this->storeId = trim((string) ($setting?->moneris_store_id ?: config('services.moneris.store_id', '')));
        $this->apiToken = trim((string) ($setting?->moneris_api_token ?: config('services.moneris.api_token', '')));
        $this->checkoutId = trim((string) ($setting?->moneris_checkout_id ?: config('services.moneris.checkout_id', '')));
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getScriptUrl(): string
    {
        return $this->scriptUrl;
    }

    /**
     * Moneris Checkout v2 Preload Request — returns a short-lived checkout ticket.
     */
    public function preload(array $params): array
    {
        if (empty($this->storeId) || empty($this->apiToken) || empty($this->checkoutId)) {
            return [
                'status' => false,
                'message' => 'Moneris credentials are not configured. Please add Store ID, API Token, and Checkout ID in Admin System Settings -> Payment Gateways.',
            ];
        }

        $payload = [
            'store_id' => $this->storeId,
            'api_token' => $this->apiToken,
            'checkout_id' => $this->checkoutId,
            'txn_total' => number_format((float) ($params['amount'] ?? 0), 2, '.', ''),
            'order_no' => $params['order_no'] ?? ('MNR-' . time()),
            'environment' => $this->environment,
            'action' => 'preload',
        ];

        if (!empty($params['email'])) {
            $payload['contact_details'] = [
                'email' => $params['email'],
                'first_name' => $params['first_name'] ?? 'Valued',
                'last_name' => $params['last_name'] ?? 'Client',
            ];
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->post($this->endpoint, $payload);

            $res = $response->json('response');
            if ($response->successful() && !empty($res['ticket']) && ($res['success'] ?? 'true') !== 'false') {
                return [
                    'status' => true,
                    'data' => [
                        'ticket' => $res['ticket'],
                        'environment' => $this->environment,
                        'script_url' => $this->scriptUrl,
                    ],
                ];
            }

            Log::error('Moneris Preload Failed', ['response' => $response->body()]);

            return [
                'status' => false,
                'message' => $res['error']['message'] ?? $res['message'] ?? 'Moneris preload request failed. Verify Store ID, API Token & Checkout ID.',
            ];
        } catch (\Throwable $e) {
            Log::error('Moneris Preload Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Moneris connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Moneris Checkout v2 Receipt Request — verifies final transaction status server-to-server.
     */
    public function receipt(string $ticket): array
    {
        if (empty($this->storeId) || empty($this->apiToken) || empty($this->checkoutId)) {
            return [
                'status' => false,
                'is_successful' => false,
                'message' => 'Moneris credentials are not configured.',
            ];
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->post($this->endpoint, [
                    'store_id' => $this->storeId,
                    'api_token' => $this->apiToken,
                    'checkout_id' => $this->checkoutId,
                    'ticket' => $ticket,
                    'environment' => $this->environment,
                    'action' => 'receipt',
                ]);

            $res = $response->json('response');
            $receipt = $res['receipt'] ?? $res;
            $cc = $receipt['cc'] ?? $receipt;

            $responseCode = $cc['response_code'] ?? $receipt['response_code'] ?? null;
            $complete = (string) ($receipt['complete'] ?? $res['complete'] ?? 'false');
            $result = strtoupper((string) ($receipt['result'] ?? ''));

            $isApproved = $response->successful()
                && ($complete === 'true' || $result === 'A')
                && $responseCode !== null
                && is_numeric($responseCode)
                && (int) $responseCode < 50;

            if ($isApproved) {
                return [
                    'status' => true,
                    'is_successful' => true,
                    'data' => $res,
                    'message' => 'Moneris payment approved (Code ' . $responseCode . ').',
                ];
            }

            Log::warning('Moneris Receipt Not Approved', ['ticket' => $ticket, 'response' => $res]);

            return [
                'status' => false,
                'is_successful' => false,
                'data' => $res,
                'message' => $cc['message'] ?? $receipt['message'] ?? 'Moneris transaction was not approved.',
            ];
        } catch (\Throwable $e) {
            Log::error('Moneris Receipt Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'is_successful' => false,
                'message' => 'Moneris verification error: ' . $e->getMessage(),
            ];
        }
    }
}
