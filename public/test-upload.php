<?php
/**
 * Direct Upload & Permission Diagnostics
 *
 * This script diagnoses why uploaded files are not saving or not visible.
 * It checks PHP configuration, folder writable states, permissions, and directory structure.
 */

header('Content-Type: text/plain');

$projectRoot = dirname(__DIR__);
$storagePath = $projectRoot . '/storage';
$appPath = $storagePath . '/app';
$publicPath = $appPath . '/public';
$uploadsPath = $publicPath . '/uploads';

echo "=== Folder Existence & Permission Analysis ===\n";
$pathsToCheck = [
    'Project Root' => $projectRoot,
    'storage' => $storagePath,
    'storage/app' => $appPath,
    'storage/app/public' => $publicPath,
    'storage/app/public/uploads' => $uploadsPath,
];

foreach ($pathsToCheck as $name => $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path))['name'] : fileowner($path);
        $isWritable = is_writable($path) ? "WRITABLE" : "NOT WRITABLE ❌";
        echo "{$name}: EXISTS (Perms: {$perms}, Owner: {$owner}) - {$isWritable}\n";
    } else {
        echo "{$name}: DOES NOT EXIST ❌ (Attempting to create...)\n";
        @mkdir($path, 0755, true);
        @chmod($path, 0755);
        if (file_exists($path)) {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            echo " -> Successfully created {$name} with perms {$perms}!\n";
        } else {
            echo " -> ❌ Failed to create {$name}.\n";
        }
    }
}

echo "\n=== PHP Upload Configuration ===\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
$tmpDir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
echo "Temporary upload directory: {$tmpDir}\n";
echo "Temporary directory writable: " . (is_writable($tmpDir) ? "YES" : "NO ❌") . "\n";

echo "\n=== Testing File Write Operation ===\n";
if (is_dir($uploadsPath)) {
    $testFile = $uploadsPath . '/write_test.txt';
    $testContent = "Upload system write test. Created at: " . date('Y-m-d H:i:s');
    
    if (@file_put_contents($testFile, $testContent)) {
        echo "✅ Success: Able to write files into storage/app/public/uploads!\n";
        echo "Verify read: " . (file_get_contents($testFile) === $testContent ? "✅ Read Success" : "❌ Read Failed") . "\n";
        unlink($testFile);
    } else {
        echo "❌ Error: Failed to write test file. Check permissions on: {$uploadsPath}\n";
    }
}

echo "\n=== Checking Symlink Integrity ===\n";
$publicStorageLink = $projectRoot . '/public/storage';
if (is_link($publicStorageLink)) {
    $target = readlink($publicStorageLink);
    echo "public/storage is a SYMLINK pointing to: {$target}\n";
    echo "Link target exists: " . (file_exists($publicStorageLink) ? "YES" : "NO (Broken Link! ❌)") . "\n";
} elseif (is_dir($publicStorageLink)) {
    echo "⚠️ Warning: public/storage is a PHYSICAL DIRECTORY, not a symlink!\n";
} else {
    echo "❌ Error: public/storage link does not exist.\n";
}
