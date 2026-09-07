<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$simRecords = \App\Models\ServiceRequest::where('payment_reference', 'LIKE', 'CREDO-SIM-%')->get();
echo "Found " . $simRecords->count() . " CREDO-SIM records in DB.\n";

foreach ($simRecords as $req) {
    echo "Request ID: {$req->id}, Ref: {$req->reference_number}, Payment Ref: {$req->payment_reference}, Status: {$req->status}, Payment Status: {$req->payment_status}\n";
    // Reset fake CREDO-SIM test records back to Unpaid so admin / user can test real Credo transaction
    $req->update([
        'payment_status' => 'Pending',
        'status' => 'Submitted',
        'payment_reference' => null,
        'amount_paid' => 0,
        'outstanding_balance' => $req->price,
    ]);
    echo "  -> Cleaned up Request ID {$req->id} back to Pending / Submitted.\n";
}
