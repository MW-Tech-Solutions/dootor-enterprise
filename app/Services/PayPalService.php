<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $mode;

    public function __construct()
    {
        $setting = SystemSetting::first();
        $this->mode = strtolower(trim((string) ($setting?->payment_mode ?: config('services.paypal.mode', 'live'))));
        $isSandbox = in_array($this->mode, ['test', 'sandbox', 'qa']);

        $this->baseUrl = $isSandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $this->clientId = trim((string) ($setting?->paypal_client_id ?: config('services.paypal.client_id', '')));
        $this->clientSecret = trim((string) ($setting?->paypal_client_secret ?: config('services.paypal.client_secret', '')));
    }

    /**
     * Obtain OAuth 2.0 access token from PayPal.
     */
    protected function getAccessToken(): ?string
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('PayPal OAuth Token Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('PayPal OAuth Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Create a PayPal Order (REST API v2).
     */
    public function createOrder(array $params): array
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            return [
                'status' => false,
                'message' => 'PayPal credentials are not configured. Please add Client ID and Client Secret in Admin System Settings -> Payment Gateways.',
            ];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'status' => false,
                'message' => 'Unable to authenticate with PayPal (' . strtoupper($this->mode) . '). Verify your PayPal Client ID & Secret.',
            ];
        }

        $amount = number_format((float) ($params['amount'] ?? 0), 2, '.', '');
        $currency = strtoupper(trim((string) ($params['currency'] ?? 'USD')));
        $reference = $params['reference'] ?? ('PP-' . time());

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $reference,
                    'description' => $params['description'] ?? 'DOOTOR Enterprises Consular & Document Service',
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount,
                    ],
                ],
            ],
            'application_context' => [
                'brand_name' => $params['brand_name'] ?? 'DOOTOR ENTERPRISES',
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW',
                'return_url' => $params['return_url'] ?? route('payment.paypal.callback'),
                'cancel_url' => $params['cancel_url'] ?? route('payment.paypal.cancel'),
            ],
        ];

        try {
            $response = Http::timeout(20)
                ->withToken($token)
                ->acceptJson()
                ->post($this->baseUrl . '/v2/checkout/orders', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $approveUrl = null;
                foreach ($data['links'] ?? [] as $link) {
                    if (($link['rel'] ?? '') === 'approve' || ($link['rel'] ?? '') === 'payer-action') {
                        $approveUrl = $link['href'];
                        break;
                    }
                }

                if (!empty($data['id']) && $approveUrl) {
                    return [
                        'status' => true,
                        'data' => [
                            'order_id' => $data['id'],
                            'authorizationUrl' => $approveUrl,
                            'reference' => $data['id'],
                        ],
                    ];
                }
            }

            Log::error('PayPal Create Order Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'PayPal order creation failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('PayPal Create Order Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Capture an approved PayPal Order (REST API v2).
     */
    public function captureOrder(string $orderId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'status' => false,
                'is_successful' => false,
                'message' => 'Unable to authenticate with PayPal to capture payment.',
            ];
        }

        try {
            $response = Http::timeout(20)
                ->withToken($token)
                ->withHeaders(['Prefer' => 'return=representation'])
                ->post($this->baseUrl . "/v2/checkout/orders/{$orderId}/capture", (object) []);

            $data = $response->json();
            $orderStatus = strtoupper((string) ($data['status'] ?? ''));

            if ($response->successful() && $orderStatus === 'COMPLETED') {
                return [
                    'status' => true,
                    'is_successful' => true,
                    'data' => $data,
                    'message' => 'PayPal payment captured and verified.',
                ];
            }

            // If already captured, verify order status
            if ($response->status() === 422 && str_contains(json_encode($data), 'ORDER_ALREADY_CAPTURED')) {
                return [
                    'status' => true,
                    'is_successful' => true,
                    'data' => $data,
                    'message' => 'PayPal order was already captured.',
                ];
            }

            Log::error('PayPal Capture Failed', ['order_id' => $orderId, 'body' => $response->body()]);

            return [
                'status' => false,
                'is_successful' => false,
                'message' => $data['message'] ?? 'PayPal payment capture was not completed.',
            ];
        } catch (\Throwable $e) {
            Log::error('PayPal Capture Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'is_successful' => false,
                'message' => 'PayPal capture error: ' . $e->getMessage(),
            ];
        }
    }
}
