<?php
/**
 * Dootor Enterprise - GitHub Deployment Webhook
 *
 * GitHub Actions calls this endpoint via HTTP after each push.
 * This script downloads the latest code from GitHub as a ZIP and extracts it.
 * NO terminal, git, or SSH access required on the server.
 *
 * SETUP:
 *  1. Upload this file to your server's /public/ folder.
 *  2. Add DEPLOY_SECRET to GitHub repo Secrets (Settings > Secrets > Actions).
 *  3. Add DEPLOY_WEBHOOK_URL pointing to this file's URL in GitHub Secrets.
 *  4. Set $secret below to match your DEPLOY_SECRET value.
 */

// ======================================================
//  ⚙️ CONFIGURATION
// ======================================================
$secret      = 'CHANGE_THIS_TO_MATCH_YOUR_DEPLOY_SECRET'; // Must match GitHub secret DEPLOY_SECRET
$githubUser  = 'MW-Tech-Solutions';
$githubRepo  = 'dootor-enterprise';
$branch      = 'main';

// The root folder of your project on the server (one level above /public/)
$projectRoot = dirname(__DIR__);

// Files/folders to never overwrite during deployment (keeps live server configs safe)
$protectedPaths = [
    '.env',
    'public/storage',
    'storage/app',
    'storage/logs',
    'vendor',
];

// ======================================================
//  🔒 Security - Validate the secret token
// ======================================================
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';

if (!hash_equals($secret, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized. Invalid or missing deploy token.']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['status' => 'error', 'message' => 'Method Not Allowed.']));
}

// ======================================================
//  📥 Download Latest Code from GitHub as ZIP
// ======================================================
$zipUrl  = "https://github.com/{$githubUser}/{$githubRepo}/archive/refs/heads/{$branch}.zip";
$tmpZip  = sys_get_temp_dir() . "/deploy_{$githubRepo}_" . time() . ".zip";
$tmpDir  = sys_get_temp_dir() . "/deploy_{$githubRepo}_extracted_" . time();

$log = [];
$log[] = "📥 Downloading latest code from GitHub...";
$log[] = "URL: {$zipUrl}";

// Download the ZIP
$context = stream_context_create([
    'http' => [
        'timeout'         => 120,
        'follow_location' => true,
        'user_agent'      => 'PHP-Deployment-Webhook/1.0',
    ]
]);

$zipContent = @file_get_contents($zipUrl, false, $context);

if ($zipContent === false) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Failed to download ZIP from GitHub.', 'log' => $log]));
}

file_put_contents($tmpZip, $zipContent);
$log[] = "✅ ZIP downloaded successfully (" . round(strlen($zipContent) / 1024) . " KB)";

// ======================================================
//  📦 Extract the ZIP
// ======================================================
$log[] = "📦 Extracting ZIP to temp directory...";

$zip = new ZipArchive();
if ($zip->open($tmpZip) !== true) {
    unlink($tmpZip);
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Failed to open downloaded ZIP file.', 'log' => $log]));
}

$zip->extractTo($tmpDir);
$zip->close();
unlink($tmpZip);

$log[] = "✅ ZIP extracted successfully";

// The extracted root folder is named: reponame-branch (e.g. dootor-enterprise-main)
$extractedFolder = $tmpDir . "/{$githubRepo}-{$branch}";

if (!is_dir($extractedFolder)) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => "Extracted folder not found: {$extractedFolder}", 'log' => $log]));
}

// ======================================================
//  🚀 Copy New Files to Project Root (skipping protected paths)
// ======================================================
$log[] = "🚀 Deploying files to: {$projectRoot}";

function copyDirectory($src, $dst, $protectedPaths, $projectRoot, &$log)
{
    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }

    $items = scandir($src);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $srcPath = $src . DIRECTORY_SEPARATOR . $item;
        $dstPath = $dst . DIRECTORY_SEPARATOR . $item;

        // Get relative path from project root for protection check
        $relativePath = ltrim(str_replace($projectRoot, '', $dstPath), DIRECTORY_SEPARATOR . '/');

        foreach ($protectedPaths as $protected) {
            if ($relativePath === $protected || strpos($relativePath, $protected . '/') === 0) {
                $log[] = "🔒 Skipped (protected): {$relativePath}";
                continue 2;
            }
        }

        if (is_dir($srcPath)) {
            copyDirectory($srcPath, $dstPath, $protectedPaths, $projectRoot, $log);
        } else {
            copy($srcPath, $dstPath);
        }
    }
}

copyDirectory($extractedFolder, $projectRoot, $protectedPaths, $projectRoot, $log);
$log[] = "✅ Files deployed successfully!";

// Cleanup temp directory
function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    rmdir($dir);
}
deleteDirectory($tmpDir);
$log[] = "🧹 Temp files cleaned up";

// ======================================================
//  ✅ Done
// ======================================================
http_response_code(200);
echo json_encode([
    'status'  => 'success',
    'message' => '🎉 Deployment complete!',
    'branch'  => $branch,
    'log'     => $log,
], JSON_PRETTY_PRINT);
