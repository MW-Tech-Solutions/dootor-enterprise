<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$credoService = new \App\Services\CredoService();

echo "Testing Credo Initialization with 580 USD...\n";

$res1 = $credoService->initializePayment([
    'amount' => 580,
    'currency' => 'USD',
    'email' => 'muhd.maher4u@gmail.com',
    'reference' => 'TEST-USD-' . time(),
]);

echo "Response 1 (USD): " . json_encode($res1, JSON_PRETTY_PRINT) . "\n\n";

$res2 = $credoService->initializePayment([
    'amount' => 580,
    'currency' => 'NGN',
    'email' => 'muhd.maher4u@gmail.com',
    'reference' => 'TEST-NGN-' . time(),
]);

echo "Response 2 (NGN): " . json_encode($res2, JSON_PRETTY_PRINT) . "\n\n";
