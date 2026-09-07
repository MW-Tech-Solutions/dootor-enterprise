<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "--- Testing CountriesNow All Countries Endpoint ---\n";

try {
    $res = Http::timeout(5)->get('https://countriesnow.space/api/v0.1/countries');
    echo "Status: " . $res->status() . "\n";
    if ($res->successful()) {
        $json = $res->json();
        $data = $json['data'] ?? [];
        echo "Total countries returned: " . count($data) . "\n";
        echo "Sample element: " . json_encode($data[0] ?? []) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
