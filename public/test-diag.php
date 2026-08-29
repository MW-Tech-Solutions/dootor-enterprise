<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Dootor Enterprises File & Config Diagnosis</h2>";
$basePath = realpath(__DIR__ . '/../');
echo "<strong>Base path:</strong> " . $basePath . "<br><br>";

// 1. Check if files exist
$filesToCheck = [
    'app/Http/Controllers/Api/Admin/DashboardController.php',
    'app/Http/Controllers/Api/Vendor/DashboardController.php',
    'app/Http/Controllers/Api/Client/DashboardController.php',
    'app/Http/Controllers/Web/PublicController.php',
    'routes/web.php',
    'routes/api.php',
];

echo "<h3>📂 File Existence Checks:</h3>";
echo "<ul>";
foreach ($filesToCheck as $file) {
    $exists = file_exists($basePath . '/' . $file);
    echo "<li><strong>$file:</strong> " . ($exists ? "<span style='color:green;'>EXISTS</span>" : "<span style='color:red;'>MISSING ❌</span>") . "</li>";
}
echo "</ul>";

// 2. Check Composer Classmap
echo "<h3>📦 Composer Autoload Check:</h3>";
if (file_exists($basePath . '/vendor/composer/autoload_classmap.php')) {
    $classmap = include $basePath . '/vendor/composer/autoload_classmap.php';
    $searchClass = 'App\\Http\\Controllers\\Api\\Admin\\DashboardController';
    $isRegistered = isset($classmap[$searchClass]);
    echo "Class <code>$searchClass</code> registered in classmap: " . ($isRegistered ? "<span style='color:green;'>YES</span>" : "<span style='color:red;'>NO ❌</span>") . "<br>";
} else {
    echo "<span style='color:red;'>autoload_classmap.php not found!</span><br>";
}

// 3. Request and Route matching diagnostics
echo "<h3>🌐 Request Routing Check:</h3>";
try {
    require_once $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $request = Illuminate\Http\Request::capture();
    echo "<strong>Request URI:</strong> <code>" . htmlspecialchars($request->getRequestUri()) . "</code><br>";
    echo "<strong>Path Info:</strong> <code>" . htmlspecialchars($request->getPathInfo()) . "</code><br>";
    echo "<strong>Base URL:</strong> <code>" . htmlspecialchars($request->getBaseUrl()) . "</code><br>";
    echo "<strong>Request Method:</strong> <code>" . htmlspecialchars($request->getMethod()) . "</code><br>";
    
    try {
        $route = Route::getRoutes()->match($request);
        echo "<strong>Matched Route Name:</strong> <code>" . htmlspecialchars($route->getName() ?? 'Unnamed') . "</code><br>";
    } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
        echo "<span style='color:red;'><strong>No matching route found for this Path Info!</strong></span><br>";
    } catch (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
        echo "<span style='color:red;'><strong>Method Not Allowed (405) for this route!</strong> Allowed methods: " . implode(', ', $e->getAllowedMethods()) . "</span><br>";
    }
    
    // Simulate incoming requests
    echo "<h4>🧪 Simulation Tests:</h4>";
    
    $testRequest1 = Illuminate\Http\Request::create('/dootor-enterprise/', 'GET');
    try {
        $route1 = Route::getRoutes()->match($testRequest1);
        echo "Simulated Request for '/dootor-enterprise/' (GET): <span style='color:green;'>SUCCESS (Matches route: " . ($route1->getName() ?? 'unnamed') . ")</span><br>";
    } catch (\Exception $e) {
        echo "Simulated Request for '/dootor-enterprise/' (GET): <span style='color:red;'>FAILED: " . $e->getMessage() . "</span><br>";
    }
    
    $testRequest2 = Illuminate\Http\Request::create('/', 'GET');
    try {
        $route2 = Route::getRoutes()->match($testRequest2);
        echo "Simulated Request for '/' (GET): <span style='color:green;'>SUCCESS (Matches route: " . ($route2->getName() ?? 'unnamed') . ")</span><br>";
    } catch (\Exception $e) {
        echo "Simulated Request for '/' (GET): <span style='color:red;'>FAILED: " . $e->getMessage() . "</span><br>";
    }
} catch (\Throwable $e) {
    echo "<span style='color:red;'>Error running request check: " . $e->getMessage() . "</span><br>";
}
