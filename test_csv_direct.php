<?php
/**
 * Test CSV Download Directly
 * This file simulates the CSV download process to verify if it works
 */

// Start session to prevent issues
session_start();

// Set up Laravel-like environment manually
$baseDir = __DIR__;
require_once $baseDir . '/vendor/autoload.php';

// Bootstrap Laravel manually for our test
$app = require_once $baseDir . '/bootstrap/app.php';

echo "=== Direct CSV Download Test ===\n";

$plantId = '65f20fa1-047a-4379-8464-59f1d94be3c7';
$selectedDate = '2025-06-14';
$chart = 'energy';

echo "Testing CSV download for:\n";
echo "Plant ID: {$plantId}\n";
echo "Date: {$selectedDate}\n";
echo "Chart: {$chart}\n\n";

try {
    // Create the controller
    $controller = new App\Http\Controllers\DownloadController();
    
    // Create a mock request
    $request = \Illuminate\Http\Request::create(
        "/plants/{$plantId}/download/{$chart}/csv",
        'GET',
        ['date' => $selectedDate]
    );
    
    // Call the download method
    $response = $controller->download($plantId, $chart, 'csv', $request);
    
    if ($response instanceof \Illuminate\Http\Response) {
        echo "✅ CSV download method executed successfully!\n";
        echo "Response type: " . get_class($response) . "\n";
        
        $headers = $response->headers->all();
        echo "Response headers:\n";
        foreach ($headers as $key => $values) {
            echo "  {$key}: " . implode(', ', $values) . "\n";
        }
        
        // For streaming response, we can't easily get the content, but we know it worked
        if ($response instanceof \Symfony\Component\HttpFoundation\StreamedResponse) {
            echo "✅ Streaming response created - CSV data should be available\n";
        } else {
            echo "Content preview: " . substr($response->getContent(), 0, 200) . "...\n";
        }
        
    } else {
        echo "❌ Unexpected response type: " . get_class($response) . "\n";
        echo "Response: " . print_r($response, true) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error testing CSV download: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
