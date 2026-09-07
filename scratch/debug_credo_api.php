<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$secretKey = env('CREDO_SECRET_KEY');
$baseUrl = env('CREDO_BASE_URL', 'https://api.credocentral.com');

$callbackUrl = env('CREDO_CALLBACK_URL') ?: env('CREDO_FRONTEND_CALLBACK_URL', 'https://dootor-enterprise.kisprojectslab.com/payment/credo/callback');
$cleanCallbackUrl = str_replace(' ', '%20', $callbackUrl);

$testPayloads = [
    'Valid HTTPS Callback URL (580)' => [
        'amount' => 580,
        'currency' => 'USD',
        'email' => 'muhd.maher4u@gmail.com',
        'callbackUrl' => $cleanCallbackUrl,
        'reference' => 'REF-HTTPS-' . time(),
        'customerFirstName' => 'Muhammad',
        'customerLastName' => 'Adamu',
        'phoneNumber' => '08012345678',
    ],
    'Valid HTTPS Callback URL Kobo (58000)' => [
        'amount' => 58000,
        'currency' => 'NGN',
        'email' => 'muhd.maher4u@gmail.com',
        'callbackUrl' => $cleanCallbackUrl,
        'reference' => 'REF-HTTPS-NGN-' . time(),
        'customerFirstName' => 'Muhammad',
        'customerLastName' => 'Adamu',
        'phoneNumber' => '08012345678',
    ],
];

foreach ($testPayloads as $name => $payload) {
    echo "--- Testing $name ---\n";
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => $secretKey,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post(rtrim($baseUrl, '/') . '/transaction/initialize', $payload);

    echo "HTTP Code: " . $response->status() . "\n";
    echo "Body: " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
}
