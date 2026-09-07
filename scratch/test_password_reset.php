<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test code generation logic
$code = strtoupper(substr(str_shuffle('23456789ABCDEFGHJKLMNPQRSTUVWXYZ'), 0, 6));
echo "Generated Alphanumeric Code: " . $code . "\n";
echo "Code Length: " . strlen($code) . "\n";
echo "Alphanumeric Check: " . (preg_match('/^[2-9A-Z]{6}$/', $code) ? "PASSED" : "FAILED") . "\n";

// Verify routes registered
$routes = [
    'password.request',
    'password.email',
    'password.code',
    'password.verify-code',
    'password.new',
    'password.update-web',
];

foreach ($routes as $routeName) {
    if (\Illuminate\Support\Facades\Route::has($routeName)) {
        echo "Route '{$routeName}': " . route($routeName) . "\n";
    } else {
        echo "Route '{$routeName}': NOT FOUND\n";
    }
}
