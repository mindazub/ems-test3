<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\DownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

echo "Content-Type: text/plain\n\n";
echo "=== Download Test ===\n";

try {
    $controller = new DownloadController();
    
    // Test plant ID
    $plantId = '65f20fa1-047a-4379-8464-59f1d94be3c7';
    $selectedDate = '2024-06-10';
    
    echo "Testing with Plant ID: {$plantId}\n";
    echo "Date: {$selectedDate}\n\n";
    
    // Use reflection to access private methods
    $reflection = new ReflectionClass($controller);
    
    // Test data retrieval
    $dataMethod = $reflection->getMethod('getPlantChartData');
    $dataMethod->setAccessible(true);
    
    echo "1. Testing data retrieval...\n";
    $data = $dataMethod->invoke($controller, $plantId, $selectedDate);
    
    echo "   Data keys: " . implode(', ', array_keys($data)) . "\n";
    echo "   Energy entries: " . count($data['energy_chart'] ?? []) . "\n";
    echo "   Battery entries: " . count($data['battery_price'] ?? []) . "\n";
    echo "   Savings entries: " . count($data['battery_savings'] ?? []) . "\n";
    
    // Test fallback method
    $fallbackMethod = $reflection->getMethod('getFallbackChartData');
    $fallbackMethod->setAccessible(true);
    
    echo "\n2. Testing fallback data...\n";
    $fallbackData = $fallbackMethod->invoke($controller, $selectedDate);
    
    echo "   Fallback energy entries: " . count($fallbackData['energy_chart'] ?? []) . "\n";
    echo "   Fallback battery entries: " . count($fallbackData['battery_price'] ?? []) . "\n";
    echo "   Fallback savings entries: " . count($fallbackData['battery_savings'] ?? []) . "\n";
    
    // Show sample fallback data
    if (!empty($fallbackData['energy_chart'])) {
        $sample = array_values($fallbackData['energy_chart'])[0];
        echo "   Sample: PV={$sample['pv_p']}W, Battery={$sample['battery_p']}W, Grid={$sample['grid_p']}W, Load={$sample['load_p']}W\n";
    }
    
    echo "\n3. Testing CSV generation...\n";
    
    // Create a request object
    $request = Request::create('/test', 'GET', ['date' => $selectedDate]);
    
    // Test CSV method
    $csvMethod = $reflection->getMethod('downloadCSV');
    $csvMethod->setAccessible(true);
    
    echo "   Generating CSV for energy chart...\n";
    $csvResponse = $csvMethod->invoke($controller, $plantId, 'energy', $selectedDate);
    
    echo "   CSV Response type: " . get_class($csvResponse) . "\n";
    
    if (method_exists($csvResponse, 'getHeaders')) {
        $headers = $csvResponse->getHeaders();
        echo "   CSV Headers: " . json_encode($headers) . "\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
?>
