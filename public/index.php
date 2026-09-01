<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Polyfill mb_split if host PHP lacks native mbstring extension
if (!function_exists('mb_split')) {
    function mb_split($pattern, $string, $limit = -1) {
        return preg_split('/' . $pattern . '/u', $string, $limit);
    }
}

// Force base path correction for cPanel subfolder deployment
if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false) {
    $_SERVER['SCRIPT_NAME'] = preg_replace('/\/public\/index\.php/', '/index.php', $_SERVER['SCRIPT_NAME']);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Create required storage directories if they are missing
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

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
