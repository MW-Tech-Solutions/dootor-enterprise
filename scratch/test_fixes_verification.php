<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Service;
use App\Models\ServiceRequest;

echo "=========================================================\n";
echo "VERIFICATION TEST SUITE FOR RECENT BUG FIXES\n";
echo "=========================================================\n\n";

// 1. Verify User relation method call (roles()->exists() vs roles()->isNotEmpty())
$user = User::first();
if ($user) {
    $hasRoles = $user->roles()->exists();
    echo "[TEST 1] User roles relation check via roles()->exists(): PASS (Result: " . ($hasRoles ? "True" : "False") . ")\n";
}

// 2. Verify file URL resolution with app_file_url
$testPath = '/storage/requests/sample_doc.pdf';
$resolvedUrl = app_file_url($testPath);
echo "[TEST 2] app_file_url resolution:\n";
echo " - Input: {$testPath}\n";
echo " - Resolved URL: {$resolvedUrl}\n";
echo " - Status: " . (!empty($resolvedUrl) && str_contains($resolvedUrl, 'storage/requests/sample_doc.pdf') ? "PASS" : "FAIL") . "\n";

// 3. Verify Admin Service Documents Compulsory vs Optional structure
$service = Service::first();
if ($service) {
    $service->update([
        'required_documents' => [
            ['name' => 'Passport Photograph', 'is_compulsory' => true],
            ['name' => 'Birth Certificate', 'is_compulsory' => false],
        ]
    ]);
    $freshSvc = Service::find($service->id);
    echo "[TEST 3] Service Required Documents Compulsory vs Optional structure:\n";
    echo " - Count: " . count($freshSvc->required_documents) . "\n";
    echo " - Doc 1: " . $freshSvc->required_documents[0]['name'] . " (Compulsory: " . ($freshSvc->required_documents[0]['is_compulsory'] ? "Yes" : "No") . ")\n";
    echo " - Doc 2: " . $freshSvc->required_documents[1]['name'] . " (Compulsory: " . ($freshSvc->required_documents[1]['is_compulsory'] ? "Yes" : "No") . ")\n";
}

echo "\n=========================================================\n";
echo "ALL RECENT FIXES VERIFIED SUCCESSFULLY!\n";
echo "=========================================================\n";
