<?php

$sqlFile = __DIR__ . '/../dootor_enterprises_updated.sql';
$handle = fopen($sqlFile, 'rb');
$first100Bytes = fread($handle, 100);
fclose($handle);

echo "First 100 Bytes (hex): " . bin2hex($first100Bytes) . "\n";
echo "First 100 Bytes (text):\n" . $first100Bytes . "\n";
