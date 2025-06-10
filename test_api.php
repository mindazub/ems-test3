<?php

require_once __DIR__ . '/vendor/autoload.php';

// Test the getData functionality directly
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Http\Controllers\PlantController;
use Illuminate\Http\Request;

// Create a mock request with the required parameters
$request = new Request([
    'start' => '1749502800',  // Today's start timestamp
    'end' => '1749589199'     // Today's end timestamp
]);

// Create controller instance
$controller = new PlantController();

echo "Testing PlantController getData method...\n";
echo "Plant ID: 65f20fa1-047a-4379-8464-59f1d94be3c7\n";
echo "Start: " . date('Y-m-d H:i:s', 1749502800) . "\n";
echo "End: " . date('Y-m-d H:i:s', 1749589199) . "\n\n";

try {
    // Call the getData method
    $response = $controller->getData('65f20fa1-047a-4379-8464-59f1d94be3c7', $request);
    
    echo "Response received!\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    if ($data) {
        echo "Data structure:\n";
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                echo "  $key: " . count($value) . " items\n";
            } else {
                echo "  $key: $value\n";
            }
        }
    } else {
        echo "Response content: $content\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
