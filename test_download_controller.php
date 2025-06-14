#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\DownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Download Controller Test ===\n";

try {
    // Create a mock request
    $request = new Request();
    $request->merge([
        'date' => '2024-06-10'
    ]);
    
    // Create controller instance
    $controller = new DownloadController();
    
    echo "\n1. Testing CSV Download...\n";
    
    // Use reflection to call private method for testing
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('downloadCSV');
    $method->setAccessible(true);
    
    $plantId = '65f20fa1-047a-4379-8464-59f1d94be3c7';
    $chart = 'energy';
    $selectedDate = '2024-06-10';
    
    echo "   Plant ID: {$plantId}\n";
    echo "   Chart: {$chart}\n";
    echo "   Date: {$selectedDate}\n";
    
    $result = $method->invoke($controller, $plantId, $chart, $selectedDate);
    
    echo "   Result type: " . get_class($result) . "\n";
    
    // Test the data retrieval method
    echo "\n2. Testing Chart Data Retrieval...\n";
    
    $dataMethod = $reflection->getMethod('getPlantChartData');
    $dataMethod->setAccessible(true);
    
    $chartData = $dataMethod->invoke($controller, $plantId, $selectedDate);
    
    echo "   Data keys: " . implode(', ', array_keys($chartData)) . "\n";
    echo "   Energy chart entries: " . count($chartData['energy_chart'] ?? []) . "\n";
    echo "   Battery chart entries: " . count($chartData['battery_price'] ?? []) . "\n";
    echo "   Savings chart entries: " . count($chartData['battery_savings'] ?? []) . "\n";
    
    // Show sample data
    if (!empty($chartData['energy_chart'])) {
        $sampleKey = array_keys($chartData['energy_chart'])[0];
        $sampleData = $chartData['energy_chart'][$sampleKey];
        echo "   Sample energy data: ";
        echo "PV=" . ($sampleData['pv_p'] ?? 0) . "W, ";
        echo "Battery=" . ($sampleData['battery_p'] ?? 0) . "W, ";
        echo "Grid=" . ($sampleData['grid_p'] ?? 0) . "W, ";
        echo "Load=" . ($sampleData['load_p'] ?? 0) . "W\n";
    }
    
    echo "\n3. Testing Plant Fetch...\n";
    
    $plantMethod = $reflection->getMethod('fetchPlantFromAPI');
    $plantMethod->setAccessible(true);
    
    try {
        $plant = $plantMethod->invoke($controller, $plantId);
        echo "   Plant found: " . ($plant ? 'YES' : 'NO') . "\n";
        if ($plant) {
            echo "   Plant name: " . ($plant->name ?? $plant->uid ?? 'unknown') . "\n";
        }
    } catch (Exception $e) {
        echo "   Plant fetch failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
