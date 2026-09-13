<?php

$files = [
    'dootor_enterprises_updated.sql',
    'dootor_enterprises_code.zip',
    'dootor_enterprises_deployment.zip'
];

foreach ($files as $f) {
    $fullPath = __DIR__ . '/../' . $f;
    if (file_exists($fullPath)) {
        $size = filesize($fullPath);
        if ($size > 1024 * 1024) {
            echo $f . ': ' . round($size / (1024 * 1024), 2) . " MB\n";
        } else {
            echo $f . ': ' . round($size / 1024, 2) . " KB\n";
        }
    } else {
        echo $f . ": Not yet created\n";
    }
}
