<?php
/**
 * Direct Storage Link Repair Script
 *
 * This script fixes broken or missing storage symbolic links.
 * On cPanel, Laravel's default absolute symlinks often fail.
 * Recreating them as relative symlinks (../storage/app/public) works perfectly.
 */

header('Content-Type: text/plain');

$projectRoot = dirname(__DIR__);
$publicStorage = $projectRoot . '/public/storage';
$targetStorage = '../storage/app/public';

echo "Project Root: {$projectRoot}\n";
echo "Public Storage Link Path: {$publicStorage}\n";

// 1. If it exists, check what it is and delete it to recreate
if (file_exists($publicStorage) || is_link($publicStorage)) {
    echo "Current storage link/folder exists. Deleting it to recreate cleanly...\n";
    if (is_link($publicStorage)) {
        if (PHP_OS_FAMILY === 'Windows') {
            exec("rmdir " . escapeshellarg($publicStorage));
        } else {
            unlink($publicStorage);
        }
    } else {
        // If it's a real directory, rename it to be safe
        rename($publicStorage, $publicStorage . '_backup_' . time());
    }
}

// 2. Create relative symbolic link
echo "Creating relative symbolic link (storage -> {$targetStorage})...\n";
try {
    // Navigate into public directory and run native ln -s to guarantee relative symlink
    if (PHP_OS_FAMILY === 'Windows') {
        // On Windows local development
        $winTarget = $projectRoot . '\storage\app\public';
        exec("mklink /D " . escapeshellarg($publicStorage) . " " . escapeshellarg($winTarget), $out, $ret);
        $success = ($ret === 0);
    } else {
        // On Linux server (cPanel)
        chdir($projectRoot . '/public');
        $success = symlink('../storage/app/public', 'storage');
    }
    
    if ($success) {
        echo "✅ Symbolic link created successfully!\n";
    } else {
        echo "❌ Failed to create symbolic link using symlink().\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// 3. Verify if target is readable through the link
if (file_exists($publicStorage)) {
    echo "\nVerification: public/storage is now READABLE.\n";
    $uploadsDir = $publicStorage . '/uploads';
    if (is_dir($uploadsDir)) {
        echo "Uploads directory found. Files list:\n";
        $files = scandir($uploadsDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo " - {$file} (" . round(filesize($uploadsDir . '/' . $file) / 1024, 2) . " KB)\n";
            }
        }
    } else {
        echo "Uploads folder is empty or not yet created.\n";
    }
} else {
    echo "\n❌ Verification: public/storage is still NOT readable.\n";
}
