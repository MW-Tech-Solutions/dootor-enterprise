<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = \App\Models\SystemSetting::first();

$secretKey = !empty($settings?->credo_secret_key) ? $settings->credo_secret_key : env('CREDO_SECRET_KEY', '');
$publicKey = !empty($settings?->credo_public_key) ? $settings->credo_public_key : env('CREDO_PUBLIC_KEY', '');
$baseUrl = !empty($settings?->credo_base_url) ? $settings->credo_base_url : env('CREDO_BASE_URL', 'https://api.credocentral.com');

echo "Current Settings:\n";
echo "Base URL: " . $baseUrl . "\n";
echo "Secret Key: " . (strlen($secretKey) > 10 ? substr($secretKey, 0, 8) . '...' : $secretKey) . "\n";
echo "Public Key: " . (strlen($publicKey) > 10 ? substr($publicKey, 0, 8) . '...' : $publicKey) . "\n\n";

$payload = [
    'amount' => 5000,
    'currency' => 'NGN',
    'email' => 'test@client.com',
    'callbackUrl' => 'https://dootor-enterprises.com/payment/credo/callback',
    'reference' => 'TEST-' . time(),
];

$urlsToTest = [
    'https://api.credocentral.com/transaction/initialize',
    'https://api.credocentral.com/v1/transaction/initialize',
    'https://api.credocentral.com/v1/transactions/initialize',
    'https://api.sandbox.credocentral.com/transaction/initialize',
];

$headersToTest = [
    'SecretKey direct' => ['Authorization' => $secretKey],
    'PublicKey direct' => ['Authorization' => $publicKey],
    'Bearer SecretKey' => ['Authorization' => 'Bearer ' . $secretKey],
    'Bearer PublicKey' => ['Authorization' => 'Bearer ' . $publicKey],
    'token header' => ['token' => $publicKey],
    'X-Api-Key' => ['X-Api-Key' => $secretKey],
];

foreach ($urlsToTest as $url) {
    foreach ($headersToTest as $hName => $header) {
        $headers = array_merge([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $header);

        try {
            $res = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders($headers)->post($url, $payload);
            echo "URL: $url | Header: $hName => Status: " . $res->status() . "\n";
            echo "Body: " . substr($res->body(), 0, 150) . "\n---\n";
        } catch (\Exception $e) {
            echo "URL: $url | Header: $hName => Exception: " . $e->getMessage() . "\n---\n";
        }
    }
}
