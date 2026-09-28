<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\ReportExportService;
use App\Services\ReportQueryService;

echo "=== 1. Testing Report Types Listing ===\n";
$superAdmin = User::where('role', 'super_admin')->orWhere('email', 'admin@test.com')->first();
if (!$superAdmin) {
    $superAdmin = User::first();
}

$types = ReportQueryService::getReportTypes($superAdmin);
echo "Super Admin sees " . count($types) . " report types.\n";
foreach ($types as $code => $info) {
    echo "  - [{$code}] => {$info['label']} ({$info['category']})\n";
}

echo "\n=== 2. Testing Database Queries for Every Report Type ===\n";
$filters = [
    'date_preset' => 'all_time',
    'search' => '',
];

foreach ($types as $code => $info) {
    try {
        $query = ReportQueryService::buildQuery($code, $filters, $superAdmin);
        $count = $query->count();
        $metrics = ReportQueryService::getSummaryMetrics($code, $filters, $superAdmin);
        echo "  ✓ [{$code}]: Query SUCCESS! Record Count: {$count} | Summary Total: {$metrics['total']}\n";
    } catch (\Throwable $e) {
        echo "  ❌ [{$code}]: Query ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== 3. Testing CSV Formatting & Formula Sanitization ===\n";
$testCell1 = "=1+2";
$testCell2 = "Normal Text";
echo "  Cell '=1+2' sanitized => " . ReportExportService::sanitizeCsvCell($testCell1) . "\n";
echo "  Cell 'Normal Text' sanitized => " . ReportExportService::sanitizeCsvCell($testCell2) . "\n";

echo "\n=== 4. Testing PDF Output Generation for 'all' Records ===\n";
try {
    $pdfResponse = ReportExportService::exportPdf('all', $filters, $superAdmin);
    $content = $pdfResponse->getContent();
    echo "  ✓ PDF Generation SUCCESS! Byte size: " . strlen($content) . " bytes.\n";
} catch (\Throwable $e) {
    echo "  ❌ PDF Generation ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== ALL REPORTING SYSTEM TESTS COMPLETE ===\n";
