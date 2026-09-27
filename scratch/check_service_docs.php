<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use App\Models\ServiceRequest;

$req = ServiceRequest::find(3);
echo "ServiceRequest ID 3:\n";
echo " - service_id: " . $req->service_id . " (" . ($req->service ? $req->service->name : 'N/A') . ")\n";
echo " - sub_service_id: " . ($req->sub_service_id ?? 'null') . " (" . ($req->subService ? $req->subService->name : 'N/A') . ")\n\n";

if ($req->service) {
    echo "Parent Service (ID " . $req->service->id . " - " . $req->service->name . ") required_documents:\n";
    var_dump($req->service->required_documents);
}

if ($req->subService) {
    echo "\nSub Service (ID " . $req->subService->id . " - " . $req->subService->name . ") required_documents:\n";
    var_dump($req->subService->required_documents);
}

echo "\nAll Services with non-empty required_documents:\n";
foreach (Service::all() as $s) {
    if (!empty($s->required_documents)) {
        echo " - Service ID " . $s->id . " ({$s->name}): " . json_encode($s->required_documents) . "\n";
    }
}
