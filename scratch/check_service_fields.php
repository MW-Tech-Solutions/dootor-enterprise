<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$setting = \App\Models\SystemSetting::first();
echo "Default Currency in DB: " . ($setting ? $setting->default_currency : 'NULL') . "\n";
echo "Currency Symbol: " . ($setting ? $setting->currency_symbol : 'NULL') . "\n\n";

echo "Services in DB:\n";
foreach (\App\Models\Service::all() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Price: {$s->price} | Parent: " . ($s->parent_id ?? 'NONE') . " | is_primary: {$s->is_primary}\n";
}

