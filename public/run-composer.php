<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>📦 Composer Autoload Regenerator</h2>";
$basePath = realpath(__DIR__ . '/../');
echo "Base path: " . $basePath . "<br><br>";

// Change directory to project root
chdir($basePath);

function runCommand($cmd) {
    echo "Running: <code>" . htmlspecialchars($cmd) . "</code><br>";
    $output = [];
    $returnValue = null;
    exec($cmd, $output, $returnValue);
    echo "Exit Code: " . $returnValue . "<br>";
    echo "Output:<br><pre style='background:#1e293b; color:#cbd5e1; padding:10px; border-radius:4px;'>" . htmlspecialchars(implode("\n", $output)) . "</pre><br>";
    return $returnValue;
}

if (function_exists('exec')) {
    // Try running composer dump-autoload using common binary locations
    $paths = ['composer', '/usr/local/bin/composer', '/usr/bin/composer', 'php composer.phar', 'php -d memory_limit=-1 /usr/local/bin/composer'];
    $success = false;
    
    foreach ($paths as $path) {
        $exitCode = runCommand("$path dump-autoload 2>&1");
        if ($exitCode === 0) {
            $success = true;
            echo "<span style='color:green;'><strong>SUCCESS:</strong> Composer autoload dumped successfully!</span><br><br>";
            break;
        }
    }
    
    if (!$success) {
        echo "<span style='color:red;'>Failed to run composer dump-autoload. You might need to run <code>composer dump-autoload</code> locally and upload the <code>vendor/composer</code> folder again.</span>";
    }
} else {
    echo "<span style='color:red;'>PHP <code>exec()</code> function is disabled on this server. Please run <code>composer dump-autoload</code> on your local machine and upload the <code>vendor/composer</code> folder to the server.</span>";
}
