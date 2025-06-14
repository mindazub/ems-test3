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
    
    echo "=== ENHANCED DATA FIX TEST RESULTS ===\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    
    if (isset($data['energy_chart'])) {
        $firstEntry = array_values($data['energy_chart'])[0] ?? null;
        $totalEntries = count($data['energy_chart']);
        
        echo "Total energy_chart entries: $totalEntries\n";
        
        if ($firstEntry) {
            echo "First entry fields: " . implode(', ', array_keys($firstEntry)) . "\n";
            echo "First entry load_p: " . ($firstEntry['load_p'] ?? 'MISSING') . "\n";
            echo "First entry battery_soc: " . ($firstEntry['battery_soc'] ?? 'MISSING') . "\n";
            
            // Check if all new fields exist
            $hasLoadData = false;
            $hasSocData = false;
            $loadValues = [];
            $socValues = [];
            
            foreach ($data['energy_chart'] as $timestamp => $entry) {
                if (isset($entry['load_p']) && $entry['load_p'] != 0) {
                    $hasLoadData = true;
                    $loadValues[] = $entry['load_p'];
                }
                if (isset($entry['battery_soc']) && $entry['battery_soc'] != 0) {
                    $hasSocData = true;
                    $socValues[] = $entry['battery_soc'];
                }
            }
            
            echo "Has non-zero load data: " . ($hasLoadData ? 'YES' : 'NO') . "\n";
            echo "Has non-zero SOC data: " . ($hasSocData ? 'YES' : 'NO') . "\n";
            
            if ($hasSocData) {
                echo "SOC values sample: " . implode(', ', array_slice($socValues, 0, 5)) . "\n";
                echo "Min SOC: " . min($socValues) . "%\n";
                echo "Max SOC: " . max($socValues) . "%\n";
            }
        }
    }
    
    // Check battery_price data
    if (isset($data['battery_price'])) {
        $firstBatteryEntry = array_values($data['battery_price'])[0] ?? null;
        echo "\nBattery Price Data:\n";
        echo "Total battery_price entries: " . count($data['battery_price']) . "\n";
        
        if ($firstBatteryEntry) {
            echo "Battery price fields: " . implode(', ', array_keys($firstBatteryEntry)) . "\n";
            echo "First entry price: " . ($firstBatteryEntry['price'] ?? 'MISSING') . "\n";
            echo "First entry tariff: " . ($firstBatteryEntry['tariff'] ?? 'MISSING') . "\n";
        }
    }
    
    // Check battery_savings data
    if (isset($data['battery_savings'])) {
        $firstSavingsEntry = array_values($data['battery_savings'])[0] ?? null;
        echo "\nBattery Savings Data:\n";
        echo "Total battery_savings entries: " . count($data['battery_savings']) . "\n";
        
        if ($firstSavingsEntry) {
            echo "Battery savings fields: " . implode(', ', array_keys($firstSavingsEntry)) . "\n";
            echo "First entry price: " . ($firstSavingsEntry['price'] ?? 'MISSING') . "\n";
        }
    }
    
    // Show sample of raw response with new fields
    echo "\nFirst 3 energy_chart entries with new fields:\n";
    $sample = array_slice($data['energy_chart'] ?? [], 0, 3, true);
    foreach ($sample as $timestamp => $entry) {
        echo "$timestamp: PV={$entry['pv_p']}, Battery={$entry['battery_p']}, Grid={$entry['grid_p']}, Load={$entry['load_p']}, SOC={$entry['battery_soc']}%\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
