<?php

$zipFile = __DIR__ . '/../dootor_enterprises_deployment.zip';
$sourceDir = realpath(__DIR__ . '/..');

if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Failed to create ZIP archive.");
}

$excludePatterns = [
    '/\.git/',
    '/node_modules/',
    '/\.gemini/',
    '/scratch/',
    '/storage\/logs\/.*\.log$/',
    '/dootor_enterprises_deployment\.zip$/',
];

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$addedCount = 0;
foreach ($files as $name => $file) {
    if (!$file->isFile()) {
        continue;
    }

    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($sourceDir) + 1);
    $relativePathNormalized = str_replace('\\', '/', $relativePath);

    $shouldExclude = false;
    foreach ($excludePatterns as $pattern) {
        if (preg_match($pattern, $relativePathNormalized)) {
            $shouldExclude = true;
            break;
        }
    }

    if ($shouldExclude) {
        continue;
    }

    $zip->addFile($filePath, $relativePathNormalized);
    $addedCount++;
}

$zip->close();

echo "Deployment ZIP created successfully!\n";
echo "Total files archived: {$addedCount}\n";
echo "Archive size: " . round(filesize($zipFile) / (1024 * 1024), 2) . " MB\n";
