<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$setting = \App\Models\SystemSetting::first();
if ($setting) {
    echo "Current SystemSetting:\n";
    echo "  payment_gateway: " . $setting->payment_gateway . "\n";
    echo "  payment_mode: " . $setting->payment_mode . "\n";
    echo "  credo_base_url: " . ($setting->credo_base_url ?? 'NULL') . "\n";
    
    $setting->update([
        'payment_gateway' => 'credo',
        'payment_mode' => 'live',
    ]);
    
    echo "Updated SystemSetting to LIVE:\n";
    echo "  payment_gateway: " . $setting->payment_gateway . "\n";
    echo "  payment_mode: " . $setting->payment_mode . "\n";
} else {
    echo "No SystemSetting record found!\n";
}
