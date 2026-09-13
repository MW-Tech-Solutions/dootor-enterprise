<?php

$sqlFile = __DIR__ . '/../dootor_enterprises_updated.sql';
$cmd = 'C:\xampp\mysql\bin\mysqldump.exe --host=127.0.0.1 --port=3308 --user=root --default-character-set=utf8mb4 dootor_enterprises';

$descriptorspec = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w']
];

$process = proc_open($cmd, $descriptorspec, $pipes);

if (is_resource($process)) {
    $output = stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);

    if (!empty($output)) {
        // Strip UTF-8 BOM if present
        $output = preg_replace('/^\xEF\xBB\xBF/', '', $output);
        file_put_contents($sqlFile, $output);
        echo "Clean UTF-8 SQL export successfully saved to " . basename($sqlFile) . "\n";
        echo "File size: " . round(filesize($sqlFile) / 1024, 2) . " KB\n";
    } else {
        echo "Error dumping MySQL: " . $errors . "\n";
    }
} else {
    echo "Failed to execute proc_open for mysqldump.\n";
}
