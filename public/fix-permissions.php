<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 File Permission Corrector</h2>";
$basePath = realpath(__DIR__ . '/../');
echo "Base path: " . $basePath . "<br>";

function chmod_r($path) {
    $dir = new DirectoryIterator($path);
    $count = 0;
    foreach ($dir as $item) {
        if ($item->isDot()) continue;
        
        $itemPath = $item->getPathname();
        if ($item->isDir()) {
            @chmod($itemPath, 0755);
            $count += chmod_r($itemPath);
            $count++;
        } else {
            @chmod($itemPath, 0644);
            $count++;
        }
    }
    return $count;
}

try {
    // Correct vendor permissions
    if (file_exists($basePath . '/vendor')) {
        echo "Correcting 'vendor' folder permissions... ";
        @chmod($basePath . '/vendor', 0755);
        $vendorCount = chmod_r($basePath . '/vendor');
        echo "Done! Adjusted permissions for $vendorCount items.<br>";
    } else {
        echo "'vendor' folder not found.<br>";
    }

    // Correct storage permissions
    if (file_exists($basePath . '/storage')) {
        echo "Correcting 'storage' folder permissions... ";
        @chmod($basePath . '/storage', 0775);
        $storageCount = chmod_r($basePath . '/storage');
        echo "Done! Adjusted permissions for $storageCount items.<br>";
    }

    // Correct bootstrap/cache permissions
    if (file_exists($basePath . '/bootstrap/cache')) {
        echo "Correcting 'bootstrap/cache' folder permissions... ";
        @chmod($basePath . '/bootstrap/cache', 0775);
        $bootstrapCount = chmod_r($basePath . '/bootstrap/cache');
        echo "Done! Adjusted permissions for $bootstrapCount items.<br>";
    }
    
    echo "<br><span style='color:green;'><strong>SUCCESS:</strong> All file permissions adjusted successfully! Try loading the setup page now.</span>";
} catch (\Throwable $e) {
    echo "<span style='color:red;'>Error during execution: " . $e->getMessage() . "</span>";
}
