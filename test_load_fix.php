#!/usr/bin/env php
<?php

// Simple test script to check if load_p is present in API response
// Run this script from the EMS root directory

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a mock request with authentication
$request = new \Illuminate\Http\Request();
$request->merge([
    'start' => strtotime('2025-06-12 21:00:00'),  // Yesterday 21:00
]);

// Create the controller
$controller = new \App\Http\Controllers\PlantController();

try {
    // Call the getData method
    $response = $controller->getData('65f20fa1-047a-4379-8464-59f1d94be3c7', $request);
    $data = json_decode($response->getContent(), true);
    
    echo "=== LOAD DATA FIX TEST RESULTS ===\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    
    if (isset($data['energy_chart'])) {
        $firstEntry = array_values($data['energy_chart'])[0] ?? null;
        $totalEntries = count($data['energy_chart']);
        
        echo "Total energy_chart entries: $totalEntries\n";
        
        if ($firstEntry) {
            echo "First entry fields: " . implode(', ', array_keys($firstEntry)) . "\n";
            echo "First entry load_p: " . ($firstEntry['load_p'] ?? 'MISSING') . "\n";
            
            // Check if load_p exists in any entries
            $hasLoadData = false;
            $loadValues = [];
            
            foreach ($data['energy_chart'] as $timestamp => $entry) {
                if (isset($entry['load_p']) && $entry['load_p'] != 0) {
                    $hasLoadData = true;
                    $loadValues[] = $entry['load_p'];
                }
            }
            
            echo "Has non-zero load data: " . ($hasLoadData ? 'YES' : 'NO') . "\n";
            
            if ($hasLoadData) {
                echo "Load values sample: " . implode(', ', array_slice($loadValues, 0, 5)) . "\n";
                echo "Min load: " . min($loadValues) . "W\n";
                echo "Max load: " . max($loadValues) . "W\n";
                echo "Average load: " . round(array_sum($loadValues) / count($loadValues), 2) . "W\n";
            }
        }
    } else {
        echo "ERROR: No energy_chart data in response\n";
    }
    
    // Show sample of raw response
    echo "\nFirst 3 energy_chart entries:\n";
    $sample = array_slice($data['energy_chart'] ?? [], 0, 3, true);
    foreach ($sample as $timestamp => $entry) {
        echo "$timestamp: PV={$entry['pv_p']}, Battery={$entry['battery_p']}, Grid={$entry['grid_p']}, Load={$entry['load_p']}\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
