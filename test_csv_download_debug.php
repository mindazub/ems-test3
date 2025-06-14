<?php
/**
 * Test CSV Download Functionality Debug
 * This file tests the CSV download for plant charts
 */

require_once 'vendor/autoload.php';

// Simulate the same API call that DownloadController uses
$plantId = '65f20fa1-047a-4379-8464-59f1d94be3c7'; // From your error
$selectedDate = '2025-06-14'; // Today's date

echo "=== CSV Download Debug Test ===\n";
echo "Plant ID: {$plantId}\n";
echo "Selected Date: {$selectedDate}\n\n";

// Test the API call directly
$client = new \GuzzleHttp\Client();

try {
    // Calculate timestamps (same logic as DownloadController)
    $date = \Carbon\Carbon::createFromFormat('Y-m-d', $selectedDate);
    $startOfDay = $date->copy()->startOfDay()->subHours(3)->timestamp;
    $endOfDay = $date->copy()->endOfDay()->subHours(3)->timestamp;
    
    echo "Start timestamp: {$startOfDay} (" . date('Y-m-d H:i:s', $startOfDay) . ")\n";
    echo "End timestamp: {$endOfDay} (" . date('Y-m-d H:i:s', $endOfDay) . ")\n\n";
    
    $url = "http://127.0.0.1:5001/plant_view/{$plantId}?start={$startOfDay}&end={$endOfDay}";
    $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
    
    echo "API URL: {$url}\n\n";
    
    $response = $client->request('GET', $url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ],
        'timeout' => 10,
    ]);
    
    $data = json_decode($response->getBody()->getContents(), true);
    
    echo "API Response Status: " . $response->getStatusCode() . "\n";
    echo "Data keys: " . implode(', ', array_keys($data ?? [])) . "\n";
    echo "Snapshots count: " . count($data['aggregated_data_snapshots'] ?? []) . "\n\n";
    
    // Test data formatting (same logic as DownloadController)
    if (!empty($data['aggregated_data_snapshots'])) {
        echo "=== Testing Data Formatting ===\n";
        $result = [
            'energy_chart' => [],
            'battery_price' => [],
            'battery_savings' => []
        ];
        
        $snapshots = array_slice($data['aggregated_data_snapshots'], 0, 3); // Test first 3
        
        foreach ($snapshots as $index => $snapshot) {
            if (is_array($snapshot)) {
                $snapshot = (object) $snapshot;
            }
            
            echo "Snapshot {$index}:\n";
            echo "  Type: " . gettype($snapshot) . "\n";
            echo "  Has stdClass: " . (isset($snapshot->stdClass) ? 'YES' : 'NO') . "\n";
            
            $dataSource = $snapshot;
            if (isset($snapshot->stdClass)) {
                $dataSource = $snapshot->stdClass;
                echo "  Using stdClass data source\n";
            }
            
            $timestamp = null;
            if (!empty($snapshot->timestamp)) {
                $timestamp = $snapshot->timestamp;
            } elseif (!empty($snapshot->dt)) {
                $timestamp = date('c', $snapshot->dt);
            } elseif (!empty($snapshot->stdClass->dt)) {
                $timestamp = date('c', $snapshot->stdClass->dt);
            }
            
            echo "  Timestamp: {$timestamp}\n";
            echo "  PV Power: " . ($dataSource->pv_p ?? 'MISSING') . "\n";
            echo "  Battery Power: " . ($dataSource->battery_p ?? 'MISSING') . "\n";
            echo "  Grid Power: " . ($dataSource->grid_p ?? 'MISSING') . "\n";
            echo "  Load Power: " . ($dataSource->load_p ?? 'MISSING') . "\n";
            echo "  Battery SOC: " . ($dataSource->battery_soc ?? 'MISSING') . "\n";
            echo "  Price: " . ($dataSource->price ?? 'MISSING') . "\n\n";
            
            if ($timestamp) {
                $result['energy_chart'][$timestamp] = [
                    'pv_p' => $dataSource->pv_p ?? 0,
                    'battery_p' => $dataSource->battery_p ?? 0,
                    'grid_p' => $dataSource->grid_p ?? 0,
                    'load_p' => $dataSource->load_p ?? 0
                ];
            }
        }
        
        echo "=== Formatted Results ===\n";
        echo "Energy chart entries: " . count($result['energy_chart']) . "\n";
        
        if (!empty($result['energy_chart'])) {
            echo "First entry: " . json_encode(array_values($result['energy_chart'])[0]) . "\n";
        }
        
        echo "\n=== CSV Generation Test ===\n";
        
        // Test CSV header generation
        $chartType = 'energy';
        $headers = match ($chartType) {
            'energy' => ['Timestamp', 'Time', 'PV Power (kW)', 'Battery Power (kW)', 'Grid Power (kW)', 'Load Power (kW)'],
            'battery' => ['Timestamp', 'Time', 'Battery Power (kW)', 'Energy Price (€/kWh)', 'Price (€/kWh)'],
            'savings' => ['Timestamp', 'Time', 'Battery Savings (€)'],
        };
        
        echo "CSV Headers: " . implode(', ', $headers) . "\n";
        
        // Test CSV row generation
        if (!empty($result['energy_chart'])) {
            $firstEntry = array_values($result['energy_chart'])[0];
            $firstTimestamp = array_keys($result['energy_chart'])[0];
            $timeOnly = date('H:i', strtotime($firstTimestamp));
            
            $csvRow = [
                $firstTimestamp,
                $timeOnly,
                round(($firstEntry['pv_p'] ?? 0) / 1000, 3),
                round(($firstEntry['battery_p'] ?? 0) / 1000, 3),
                round(($firstEntry['grid_p'] ?? 0) / 1000, 3),
                round(($firstEntry['load_p'] ?? 0) / 1000, 3)
            ];
            
            echo "Sample CSV Row: " . implode(', ', $csvRow) . "\n";
        }
        
        echo "\n✅ CSV Download should work - data is available and formatted correctly!\n";
        
    } else {
        echo "❌ No snapshot data available - CSV download will fail\n";
    }
    
} catch (Exception $e) {
    echo "❌ API Error: " . $e->getMessage() . "\n";
    echo "This explains why CSV downloads aren't working!\n";
}

echo "\n=== Debug Complete ===\n";
?>
