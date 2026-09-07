<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bladeContent = file_get_contents(__DIR__ . '/../resources/views/client/request-details.blade.php');
$compiled = \Illuminate\Support\Facades\Blade::compileString($bladeContent);
echo "Blade compiled successfully! Total compiled length: " . strlen($compiled) . " bytes.\n";
