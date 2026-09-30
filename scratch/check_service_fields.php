<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$s37 = \App\Models\Service::find(37);
if ($s37) {
    echo "Service 37: {$s37->name}\n";
    echo "Fields count: " . $s37->fields->count() . "\n";
    foreach ($s37->fields as $f) {
        echo "ID: {$f->id} | Label: {$f->field_label} | Type: '{$f->field_type}' | Name: {$f->field_name}\n";
    }
} else {
    echo "Service 37 not found in DB!\n";
}

echo "\nAll Services in DB:\n";
foreach (\App\Models\Service::all() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Fields count: " . $s->fields->count() . "\n";
    foreach ($s->fields as $f) {
        echo "   -> Field ID: {$f->id} | Label: {$f->field_label} | Type: '{$f->field_type}' | Name: {$f->field_name}\n";
    }
}
