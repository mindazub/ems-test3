<?php

// Test script to verify timezone calculations and API calls
echo "=== Timezone and API Call Test ===\n\n";

// Test current date (today)
$today = new DateTime('now');
echo "Current date: " . $today->format('Y-m-d H:i:s') . "\n";

// Test timezone calculations for EEST (UTC+3)
$selectedDate = $today->format('Y-m-d');
$dateTime = new DateTime($selectedDate . ' 00:00:00');

// Calculate start of day in EEST (UTC+3)
$startOfDay = $dateTime->getTimestamp() - (3 * 3600); // Subtract 3 hours for EEST offset

echo "Selected date: $selectedDate\n";
echo "Start of day (EEST midnight as UTC timestamp): $startOfDay\n";
echo "Start of day converts to: " . date('Y-m-d H:i:s T', $startOfDay) . "\n";

// Check if it's today
$isToday = $dateTime->format('Y-m-d') === $today->format('Y-m-d');
echo "Is today: " . ($isToday ? 'Yes' : 'No') . "\n";

// Expected API URLs
$plantId = '65f20fa1-047a-4379-8464-59f1d94be3c7'; // Test plant ID
if ($isToday) {
    $url = "/plants/$plantId/data?start=$startOfDay";
    echo "Today's API URL: $url\n";
} else {
    $endOfDay = $startOfDay + (24 * 3600) - 1; // End of day
    $url = "/plants/$plantId/data?start=$startOfDay&end=$endOfDay";
    echo "Historical API URL: $url\n";
}

echo "\n=== Testing a specific historical date ===\n";

// Test historical date
$historicalDate = '2025-06-09'; // Yesterday
$historicalDateTime = new DateTime($historicalDate . ' 00:00:00');
$historicalStart = $historicalDateTime->getTimestamp() - (3 * 3600);
$historicalEnd = $historicalStart + (24 * 3600) - 1;

echo "Historical date: $historicalDate\n";
echo "Historical start: $historicalStart (converts to: " . date('Y-m-d H:i:s T', $historicalStart) . ")\n";
echo "Historical end: $historicalEnd (converts to: " . date('Y-m-d H:i:s T', $historicalEnd) . ")\n";
echo "Historical API URL: /plants/$plantId/data?start=$historicalStart&end=$historicalEnd\n";

echo "\n=== Test Complete ===\n";
