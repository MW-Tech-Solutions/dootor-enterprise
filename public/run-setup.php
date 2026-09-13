<?php

/**
 * Laravel Web Setup Utility
 * 
 * Access this script via browser (e.g. http://yourdomain.com/dootor-enterprise/run-setup.php)
 * to run necessary artisan commands on servers without terminal access.
 */

// 1. Check if the script should be secured or disabled (uncomment if you want to prevent unauthorized runs)
// define('SECURE_KEY', 'some_secret_key');
// if (!isset($_GET['key']) || $_GET['key'] !== SECURE_KEY) {
//     die('Access Denied: Invalid security key.');
// }

// Create required directories if they are missing
$storageDirs = [
    __DIR__.'/../storage',
    __DIR__.'/../storage/framework',
    __DIR__.'/../storage/framework/cache',
    __DIR__.'/../storage/framework/cache/data',
    __DIR__.'/../storage/framework/sessions',
    __DIR__.'/../storage/framework/views',
    __DIR__.'/../storage/app',
    __DIR__.'/../storage/app/public',
    __DIR__.'/../storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
        @chmod($dir, 0775);
    }
}

// 2. Bootstrap Laravel
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
} catch (\Exception $e) {
    die('Failed to bootstrap Laravel application. Make sure vendor folder exists and autoload is working. Error: ' . $e->getMessage());
}

use Illuminate\Support\Facades\Artisan;

// 3. Prevent timeout issues
@set_time_limit(300);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Web Setup & Migration Utility</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #0f172a; color: #f8fafc; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background-color: #1e293b; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #334155; }
        .header { background-color: #003b1c; color: #d4af37; padding: 20px; border-bottom: 2px solid #d4af37; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #cbd5e1; font-size: 14px; }
        .console { font-family: 'Courier New', Courier, monospace; background-color: #090d16; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 13px; line-height: 1.5; color: #38bdf8; margin: 20px; border: 1px solid #1e293b; }
        .success { color: #4ade80; }
        .error { color: #f87171; font-weight: bold; }
        .cmd-box { margin-bottom: 20px; border-bottom: 1px solid #1e293b; padding-bottom: 15px; }
        .cmd-box:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .footer { padding: 15px 20px; font-size: 12px; color: #64748b; background-color: #0f172a; text-align: center; border-top: 1px solid #1e293b; }
    </style>
</head>
<body>
<div class='container'>
    <div class='header'>
        <h1>🛠️ DOOTOR ENTERPRISES Setup Wizard</h1>
        <p>Running Laravel setup, migrations, and caching optimization workflows via HTTP request</p>
    </div>
    <div class='console'>";

function runArtisanCommand($command, $parameters = []) {
    $paramString = !empty($parameters) ? ' ' . json_encode($parameters) : '';
    echo "<div class='cmd-box'>";
    echo "<span style='color: #a855f7;'>$</span> <strong>php artisan " . htmlspecialchars($command . $paramString) . "</strong><br>";
    
    try {
        $exitCode = Artisan::call($command, $parameters);
        $output = Artisan::output();
        
        if ($exitCode === 0) {
            echo "<span class='success'>[SUCCESS]</span> Command executed successfully.<br>";
        } else {
            echo "<span class='error'>[WARNING]</span> Command finished with exit code: " . $exitCode . "<br>";
        }
        
        if (!empty(trim($output))) {
            echo "<pre style='margin: 5px 0; color: #e2e8f0; white-space: pre-wrap;'>" . htmlspecialchars($output) . "</pre>";
        }
    } catch (\Exception $e) {
        echo "<span class='error'>[ERROR]</span> Failed to run command: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
    echo "</div>";
    // Flush output to browser
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

// Ensure output is flushed instantly
if (function_exists('ob_end_clean')) {
    @ob_end_clean();
}

// Start executing Laravel setup & cache refresh tasks
runArtisanCommand('config:clear');
runArtisanCommand('cache:clear');
runArtisanCommand('route:clear');
runArtisanCommand('view:clear');
runArtisanCommand('optimize:clear');

// Run pending migrations (force parameter is critical for production)
runArtisanCommand('migrate', ['--force' => true]);

// Link public storage if needed
try {
    runArtisanCommand('storage:link');
} catch (\Exception $e) {
    // Ignore if already linked
}

// Re-cache views and configuration for production performance
runArtisanCommand('config:cache');
runArtisanCommand('view:cache');

echo "</div>
    <div class='footer'>
        Setup complete. For safety, it is highly recommended to **delete** or **rename** this script (<code>public/run-setup.php</code>) now that setup is done.
    </div>
</div>
</body>
</html>";
