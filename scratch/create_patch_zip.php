<?php

$patchZip = __DIR__ . '/../dootor_enterprises_upload_fix_patch.zip';
$sourceDir = realpath(__DIR__ . '/..');

if (file_exists($patchZip)) {
    unlink($patchZip);
}

$filesToInclude = [
    'app/Helpers/FinfoPolyfill.php',
    'app/Helpers/FileUploadHelper.php',
    'bootstrap/app.php',
    'routes/web.php',
    'app/Http/Controllers/Web/ClientController.php',
    'app/Http/Controllers/Web/VendorController.php',
    'app/Http/Controllers/Web/AdminController.php',
    'app/Http/Controllers/Api/UploadController.php',
];

$zip = new ZipArchive();
if ($zip->open($patchZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Failed to create patch ZIP archive.");
}

foreach ($filesToInclude as $relPath) {
    $fullPath = $sourceDir . '/' . $relPath;
    if (file_exists($fullPath)) {
        $zip->addFile($fullPath, $relPath);
        echo "Added: {$relPath}\n";
    } else {
        echo "WARNING: File not found: {$relPath}\n";
    }
}

$zip->close();

echo "\nPatch ZIP successfully created!\n";
echo "File: " . basename($patchZip) . "\n";
echo "Size: " . round(filesize($patchZip) / 1024, 2) . " KB\n";
