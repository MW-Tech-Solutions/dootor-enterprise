<?php
/**
 * Dootor Enterprise - GitHub Deployment Webhook
 *
 * GitHub Actions calls this endpoint via HTTP after each push.
 * The server then pulls the latest code from GitHub directly.
 *
 * SETUP:
 *  1. Upload this file to your server's /public/ folder.
 *  2. In cPanel, initialize git in your project root:
 *     git clone https://github.com/MW-Tech-Solutions/dootor-enterprise.git .
 *  3. Add DEPLOY_SECRET to your GitHub repo secrets (Settings > Secrets).
 *  4. Set the same secret in the $secret variable below.
 */

// ======================================================
//  ⚙️ CONFIGURATION - Set your secret token here
// ======================================================
$secret = getenv('DEPLOY_WEBHOOK_SECRET') ?: 'CHANGE_THIS_TO_A_RANDOM_SECRET_STRING';

// ======================================================
//  🔒 Security Check - Validate the secret token
// ======================================================
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';

if (!hash_equals($secret, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized. Invalid or missing deploy token.']));
}

// Only allow POST requests from GitHub Actions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Only POST requests are accepted.']));
}

// ======================================================
//  🚀 Run Git Pull
// ======================================================
$projectRoot = dirname(__DIR__); // Go up one level from /public to project root

$output = [];
$return_code = 0;

// Run git pull
exec("cd {$projectRoot} && git pull origin main 2>&1", $output, $return_code);

$outputStr = implode("\n", $output);

if ($return_code === 0) {
    http_response_code(200);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Deployment complete!',
        'output'  => $outputStr,
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Git pull failed. Check the output for details.',
        'output'  => $outputStr,
        'code'    => $return_code,
    ]);
}
