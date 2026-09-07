<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$secretKey = env('CREDO_SECRET_KEY');
$baseUrl = env('CREDO_BASE_URL', 'https://api.credocentral.com');

$amountInUSD = 580.00;
$amountInMinorUnits = (int) round($amountInUSD * 100); // 58000

$envCallback = env('CREDO_CALLBACK_URL') ?: env('CREDO_FRONTEND_CALLBACK_URL');
$callbackUrl = $envCallback ?: route('payment.credo.callback');
$cleanCallbackUrl = str_replace(' ', '%20', $callbackUrl);
if (str_contains($cleanCallbackUrl, 'localhost')) {
    $cleanCallbackUrl = $envCallback ?: 'https://dootor-enterprises.com/payment/credo/callback';
}

$payload = [
    'amount' => $amountInMinorUnits,
    'currency' => 'USD',
    'email' => 'muhd.maher4u@gmail.com',
    'callbackUrl' => $cleanCallbackUrl,
    'reference' => 'TEST-580USD-' . time(),
];

echo "Sending Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

$response = \Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => $secretKey,
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
])->post(rtrim($baseUrl, '/') . '/transaction/initialize', $payload);

echo "HTTP Code: " . $response->status() . "\n";
echo "Credo Response: " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n";
