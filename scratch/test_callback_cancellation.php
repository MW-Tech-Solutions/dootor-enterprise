<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate request to /payment/credo/callback with cancellation parameters
$request = \Illuminate\Http\Request::create(
    '/payment/credo/callback?reference=DE-2-1788790463&transAmount=602.62&transRef=A4p500dao7878WH536LJ&errorMessage=Transaction+cancelled&currency=USD&status=9',
    'GET'
);

$controller = new \App\Http\Controllers\Web\PaymentController(new \App\Services\CredoService());
$response = $controller->callback($request);

echo "Response Status Code: " . $response->getStatusCode() . "\n";
echo "Target Redirect URL: " . $response->getTargetUrl() . "\n";
echo "Session Error Flash: " . session('error') . "\n";
