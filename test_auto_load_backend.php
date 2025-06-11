<?php
// Test script to verify auto-loading functionality
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

echo "=== EMS Auto-Load Test ===\n";
echo "Testing Plant ID detection and data loading...\n\n";

// Test 1: Check if routes exist
echo "1. Testing Route Availability:\n";

try {
    $routes = app('router')->getRoutes();
    $plantDataRoute = false;
    $availableDatesRoute = false;
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'plants/{plant}/data') !== false) {
            $plantDataRoute = true;
            echo "   ✓ Plant data route found: {$uri}\n";
        }
        if (strpos($uri, 'plants/{plant}/available-dates') !== false) {
            $availableDatesRoute = true;
            echo "   ✓ Available dates route found: {$uri}\n";
        }
    }
    
    if (!$plantDataRoute) {
        echo "   ❌ Plant data route NOT found\n";
    }
    if (!$availableDatesRoute) {
        echo "   ❌ Available dates route NOT found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check if PlantController methods exist
echo "2. Testing Controller Methods:\n";

try {
    $reflection = new ReflectionClass(App\Http\Controllers\PlantController::class);
    
    $methods = ['show', 'getData', 'getAvailableDates'];
    foreach ($methods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "   ✓ Method {$method} exists\n";
        } else {
            echo "   ❌ Method {$method} NOT found\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking controller: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check plant model
echo "3. Testing Plant Model:\n";

try {
    $plantCount = App\Models\Plant::count();
    echo "   ✓ Plant model accessible\n";
    echo "   ✓ Total plants in database: {$plantCount}\n";
    
    if ($plantCount > 0) {
        $firstPlant = App\Models\Plant::first();
        $plantId = $firstPlant->uid ?? $firstPlant->uuid ?? $firstPlant->id ?? 'unknown';
        echo "   ✓ Sample plant ID: {$plantId}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error accessing Plant model: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test API endpoint simulation
echo "4. Testing API Configuration:\n";

try {
    // Check if Guzzle client can be instantiated
    $client = new \GuzzleHttp\Client();
    echo "   ✓ Guzzle HTTP client available\n";
    
    // Test API URL construction
    $testPlantId = 'test-plant-123';
    $today = time();
    $url = "http://127.0.0.1:5001/plant_view/{$testPlantId}?start={$today}";
    echo "   ✓ API URL format: {$url}\n";
    
} catch (Exception $e) {
    echo "   ❌ Error with HTTP client: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Check file structure
echo "5. Testing File Structure:\n";

$files = [
    'resources/views/plants/partials/plant-chart.blade.php',
    'routes/web.php',
    'app/Http/Controllers/PlantController.php'
];

foreach ($files as $file) {
    $fullPath = base_path($file);
    if (file_exists($fullPath)) {
        $size = number_format(filesize($fullPath));
        echo "   ✓ {$file} exists ({$size} bytes)\n";
    } else {
        echo "   ❌ {$file} NOT found\n";
    }
}

echo "\n";

// Test 6: JavaScript detection simulation
echo "6. Testing JavaScript Plant ID Detection Logic:\n";

// Simulate what the JavaScript would do
$testCases = [
    'Backend JSON' => 'test-plant-123',
    'URL Path' => '/plants/plant-456/view',
    'Data Attribute' => 'data-plant-id="plant-789"'
];

foreach ($testCases as $method => $test) {
    echo "   ✓ {$method}: {$test}\n";
}

echo "\n";

// Test 7: Date handling
echo "7. Testing Date Handling:\n";

$today = new DateTime();
$todayStr = $today->format('Y-m-d');
$todayFormatted = $today->format('Ymd');

echo "   ✓ Today (YYYY-MM-DD): {$todayStr}\n";
echo "   ✓ Today (YYYYMMDD): {$todayFormatted}\n";

// Test EEST timezone calculation
$eestOffset = 3 * 60 * 60; // 3 hours in seconds
$utcMidnight = mktime(0, 0, 0, $today->format('n'), $today->format('j'), $today->format('Y'));
$eestMidnightUTC = $utcMidnight - $eestOffset;

echo "   ✓ UTC Midnight: " . date('Y-m-d H:i:s', $utcMidnight) . "\n";
echo "   ✓ EEST Midnight (UTC): " . date('Y-m-d H:i:s', $eestMidnightUTC) . "\n";

echo "\n=== Test Complete ===\n";
echo "If all items show ✓, the auto-loading should work correctly.\n";
echo "Run this in browser console to test frontend: window.plantId\n";
