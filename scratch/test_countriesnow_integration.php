<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

echo "--- Testing CountriesNow Integration Logic ---\n";

// 1. Fetch & Filter African Countries
$africanIsoMap = [
    'DZ' => 'Algeria', 'AO' => 'Angola', 'BJ' => 'Benin', 'BW' => 'Botswana', 'BF' => 'Burkina Faso',
    'BI' => 'Burundi', 'CV' => 'Cabo Verde', 'CM' => 'Cameroon', 'CF' => 'Central African Republic',
    'TD' => 'Chad', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Democratic Republic of the Congo',
    'DJ' => 'Djibouti', 'EG' => 'Egypt', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea',
    'SZ' => 'Eswatini', 'ET' => 'Ethiopia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GH' => 'Ghana',
    'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'CI' => 'Ivory Coast', 'KE' => 'Kenya',
    'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libya', 'MG' => 'Madagascar', 'MW' => 'Malawi',
    'ML' => 'Mali', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'MA' => 'Morocco', 'MZ' => 'Mozambique',
    'NA' => 'Namibia', 'NE' => 'Niger', 'NG' => 'Nigeria', 'RW' => 'Rwanda', 'ST' => 'Sao Tome and Principe',
    'SN' => 'Senegal', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SO' => 'Somalia', 'ZA' => 'South Africa',
    'SS' => 'South Sudan', 'SD' => 'Sudan', 'TZ' => 'Tanzania', 'TG' => 'Togo', 'TN' => 'Tunisia',
    'UG' => 'Uganda', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe'
];

try {
    $response = Http::timeout(5)->get('https://countriesnow.space/api/v0.1/countries');
    if ($response->successful()) {
        $all = $response->json('data') ?? [];
        $africanList = [];
        foreach ($all as $c) {
            $iso2 = strtoupper($c['iso2'] ?? '');
            if (isset($africanIsoMap[$iso2])) {
                $africanList[] = [
                    'name' => $africanIsoMap[$iso2], // Use standardized African name
                    'api_name' => $c['country'],
                    'code' => $iso2,
                ];
            }
        }
        echo "Successfully fetched & filtered " . count($africanList) . " African countries from CountriesNow API.\n";
        echo "Sample African country: " . json_encode($africanList[0]) . "\n";
    }
} catch (\Exception $e) {
    echo "API Error: " . $e->getMessage() . "\n";
}

// 2. Fetch States for Nigeria & Kenya
$countriesToTest = ['Nigeria', 'Kenya', 'South Africa', 'Ghana'];
foreach ($countriesToTest as $country) {
    try {
        $res = Http::timeout(5)->post('https://countriesnow.space/api/v0.1/countries/states', [
            'country' => $country
        ]);
        if ($res->successful()) {
            $rawStates = $res->json('data.states') ?? [];
            $stateNames = array_map(function ($s) {
                // Clean name e.g. "Abia State" -> "Abia" or keep original
                $name = $s['name'];
                $clean = preg_replace('/\s+(State|County|Province|Region|Governorate|District|Department)$/i', '', $name);
                return $clean ?: $name;
            }, $rawStates);
            echo "Country: {$country} | States from API: " . count($stateNames) . " | First 4: " . implode(', ', array_slice($stateNames, 0, 4)) . "\n";
        }
    } catch (\Exception $e) {
        echo "Error fetching states for {$country}: " . $e->getMessage() . "\n";
    }
}
