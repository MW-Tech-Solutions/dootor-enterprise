<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\LocationApiController;
use Illuminate\Http\Request;

$ctrl = new LocationApiController();
$req = new Request(['country' => 'Nigeria']);
$res = $ctrl->divisions($req);

echo "API Response for Nigeria:\n";
echo json_encode($res->getData(true), JSON_PRETTY_PRINT) . "\n";
