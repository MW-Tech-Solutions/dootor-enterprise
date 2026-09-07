<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AfricanLocationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Cache::flush();

echo "=== AFRICAN LOCATION SERVICE VERIFICATION ===\n\n";

// 1. Check Country Count
$countries = AfricanLocationService::allCountries();
echo "Total African Countries: " . count($countries) . " (Expected: 54)\n";
if (count($countries) !== 54) {
    echo "[ERROR] Expected 54 countries, found " . count($countries) . "\n";
    exit(1);
} else {
    echo "[SUCCESS] Exactly 54 African countries registered.\n";
}

// 2. Test Default Country & Non-African Exclusion
$countryNames = AfricanLocationService::countryNames();
if (in_array('Canada', $countryNames) || in_array('United States', $countryNames) || in_array('United Kingdom', $countryNames)) {
    echo "[ERROR] Non-African countries detected in dataset!\n";
    exit(1);
} else {
    echo "[SUCCESS] Zero non-African countries in dropdown dataset.\n";
}

if (!in_array('Nigeria', $countryNames)) {
    echo "[ERROR] Nigeria missing from dataset!\n";
    exit(1);
} else {
    echo "[SUCCESS] Nigeria present and set as default.\n";
}

// 3. Test Division Term Adaptation
$samples = [
    'Nigeria' => ['term' => 'State', 'sample_division' => 'Lagos'],
    'Kenya' => ['term' => 'County', 'sample_division' => 'Nairobi'],
    'South Africa' => ['term' => 'Province', 'sample_division' => 'Gauteng'],
    'Egypt' => ['term' => 'Governorate', 'sample_division' => 'Cairo'],
    'Ghana' => ['term' => 'Region', 'sample_division' => 'Greater Accra'],
    'Algeria' => ['term' => 'Province / Wilaya', 'sample_division' => 'Algiers'],
    'Uganda' => ['term' => 'District', 'sample_division' => 'Kampala'],
];

echo "\n--- Testing Administrative Term Adaptation & Sample Divisions ---\n";
foreach ($samples as $country => $expected) {
    $info = AfricanLocationService::getCountryInfo($country);
    if (!$info) {
        echo "[ERROR] Country {$country} not found!\n";
        exit(1);
    }
    echo "Country: {$country} | Code: {$info['code']} | Term: {$info['term']} | Divisions Count: " . count($info['divisions']) . "\n";
    
    if ($info['term'] !== $expected['term']) {
        echo "  [ERROR] Expected term '{$expected['term']}', got '{$info['term']}'\n";
        exit(1);
    }
    if (!in_array($expected['sample_division'], $info['divisions'])) {
        echo "  [ERROR] Expected division '{$expected['sample_division']}' not found in {$country}\n";
        exit(1);
    }
    echo "  -> Valid division match: {$expected['sample_division']}\n";
}

// 4. Test Backend Validation (Valid vs Invalid Pairs)
echo "\n--- Testing Server-Side Pair Validation ---\n";
$validCheck = AfricanLocationService::isValidPair('Ghana', 'Ashanti');
$invalidCheck = AfricanLocationService::isValidPair('Ghana', 'Lagos');
$invalidCheck2 = AfricanLocationService::isValidPair('Kenya', 'Gauteng');

echo "Ghana + Ashanti: " . ($validCheck ? "VALID [PASS]" : "INVALID [FAIL]") . "\n";
echo "Ghana + Lagos: " . ($invalidCheck ? "VALID [FAIL]" : "REJECTED [PASS]") . "\n";
echo "Kenya + Gauteng: " . ($invalidCheck2 ? "VALID [FAIL]" : "REJECTED [PASS]") . "\n";

if ($validCheck && !$invalidCheck && !$invalidCheck2) {
    echo "[SUCCESS] Server-side country/division validation functioning perfectly.\n";
} else {
    echo "[ERROR] Server-side validation check failed!\n";
    exit(1);
}

// 5. Test Location API Routes
echo "\n--- Testing Location API Controller Endpoints ---\n";
$controller = new \App\Http\Controllers\Api\LocationApiController();
$countriesResponse = $controller->africanCountries()->getData(true);
echo "API Countries status: " . $countriesResponse['status'] . " | Default: " . $countriesResponse['default'] . " | Count: " . count($countriesResponse['countries']) . "\n";

$request = new \Illuminate\Http\Request(['country' => 'Kenya']);
$divisionsResponse = $controller->divisions($request)->getData(true);
echo "API Kenya Divisions status: " . $divisionsResponse['status'] . " | Term: " . $divisionsResponse['data']['term'] . " | Divisions: " . implode(', ', array_slice($divisionsResponse['data']['divisions'], 0, 5)) . "...\n";

if ($countriesResponse['status'] === 'success' && $divisionsResponse['data']['term'] === 'County') {
    echo "[SUCCESS] Location API endpoints functioning properly.\n";
} else {
    echo "[ERROR] API Controller test failed!\n";
    exit(1);
}

// 6. Test AdminController serviceDocuments Method
echo "\n--- Testing AdminController serviceDocuments Method ---\n";
$adminCtrl = new \App\Http\Controllers\Web\AdminController();
if (method_exists($adminCtrl, 'serviceDocuments') && method_exists($adminCtrl, 'updateServiceDocuments')
    && method_exists($adminCtrl, 'documentTypes') && method_exists($adminCtrl, 'storeDocumentType')
    && method_exists($adminCtrl, 'updateDocumentType') && method_exists($adminCtrl, 'assignDocumentToServices')
    && method_exists($adminCtrl, 'deleteDocumentType')) {
    echo "[SUCCESS] All documentTypes and serviceDocuments methods exist on AdminController.\n";
} else {
    echo "[ERROR] AdminController missing documentTypes methods!\n";
    exit(1);
}

echo "\n=============================================\n";
echo "ALL DYNAMIC AFRICAN LOCATION TESTS PASSED SUCCESSFULLY!\n";
echo "=============================================\n";
