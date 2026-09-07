<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "--- Testing CountriesNow API ---\n";

try {
    // 1. Fetch states for Nigeria via CountriesNow API POST endpoint
    $response = Http::timeout(5)->post('https://countriesnow.space/api/v0.1/countries/states', [
        'country' => 'Nigeria'
    ]);

    echo "Status code for Nigeria states: " . $response->status() . "\n";
    if ($response->successful()) {
        $json = $response->json();
        echo "Response error flag: " . var_export($json['error'] ?? null, true) . "\n";
        echo "Msg: " . ($json['msg'] ?? '') . "\n";
        $states = $json['data']['states'] ?? [];
        echo "Found " . count($states) . " states for Nigeria. Sample: " . json_encode(array_slice($states, 0, 3)) . "\n";
    }

    // 2. Fetch states for Kenya
    $resKenya = Http::timeout(5)->post('https://countriesnow.space/api/v0.1/countries/states', [
        'country' => 'Kenya'
    ]);
    if ($resKenya->successful()) {
        $jsonK = $resKenya->json();
        $statesK = $jsonK['data']['states'] ?? [];
        echo "Found " . count($statesK) . " states/counties for Kenya. Sample: " . json_encode(array_slice($statesK, 0, 3)) . "\n";
    }

} catch (\Exception $e) {
    echo "CountriesNow API exception: " . $e->getMessage() . "\n";
}
