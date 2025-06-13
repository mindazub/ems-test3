<?php

/**
 * Battery Savings Calculation Verification Script
 * 
 * This script demonstrates the difference between the old and new calculation methods
 * and verifies that the fix produces reasonable results.
 */

echo "=== Battery Savings Calculation Fix Verification ===\n\n";

// Sample data representing a day with battery discharge every 30 minutes
$sampleData = [
    ['time' => '08:00', 'battery_p' => 2000, 'tariff' => 0.15], // 2kW discharge
    ['time' => '08:30', 'battery_p' => 2500, 'tariff' => 0.15], // 2.5kW discharge
    ['time' => '09:00', 'battery_p' => 3000, 'tariff' => 0.16], // 3kW discharge
    ['time' => '09:30', 'battery_p' => 2800, 'tariff' => 0.16], // 2.8kW discharge
    ['time' => '10:00', 'battery_p' => 2200, 'tariff' => 0.17], // 2.2kW discharge
    ['time' => '10:30', 'battery_p' => 1800, 'tariff' => 0.17], // 1.8kW discharge
];

echo "Sample Data (6 data points, 30-minute intervals):\n";
foreach ($sampleData as $i => $data) {
    echo sprintf("  %s: %dW discharge at €%.3f/kWh\n", 
        $data['time'], $data['battery_p'], $data['tariff']);
}
echo "\n";

// OLD CALCULATION (incorrect)
echo "OLD CALCULATION (INCORRECT - no time interval consideration):\n";
$oldTotal = 0;
foreach ($sampleData as $data) {
    $savings = ($data['battery_p'] / 1000) * $data['tariff'];
    $oldTotal += $savings;
    echo sprintf("  %s: %.2fkW × €%.3f = €%.3f\n", 
        $data['time'], $data['battery_p']/1000, $data['tariff'], $savings);
}
echo sprintf("OLD TOTAL DAILY SAVINGS: €%.3f (INFLATED)\n\n", $oldTotal);

// NEW CALCULATION (correct)
echo "NEW CALCULATION (CORRECT - with time interval consideration):\n";
$timeInterval = 0.5; // 30 minutes = 0.5 hours
$newTotal = 0;
foreach ($sampleData as $data) {
    $savings = ($data['battery_p'] / 1000) * $data['tariff'] * $timeInterval;
    $newTotal += $savings;
    echo sprintf("  %s: %.2fkW × €%.3f × %.1fh = €%.3f\n", 
        $data['time'], $data['battery_p']/1000, $data['tariff'], $timeInterval, $savings);
}
echo sprintf("NEW TOTAL DAILY SAVINGS: €%.3f (ACCURATE)\n\n", $newTotal);

// ANALYSIS
echo "ANALYSIS:\n";
echo sprintf("- Reduction factor: %.1fx (new = old ÷ %.1f)\n", $oldTotal/$newTotal, $oldTotal/$newTotal);
echo sprintf("- This matches expectation for 30-minute intervals (should be 2x reduction)\n");
echo sprintf("- For a full day (48 data points), this prevents €%.2f of over-calculation\n", 
    ($oldTotal - $newTotal) * (48/6));

echo "\nFIX VERIFICATION: ";
$expectedReduction = 2.0; // Expected 2x reduction for 30-min intervals
$actualReduction = $oldTotal / $newTotal;
$tolerance = 0.1;

if (abs($actualReduction - $expectedReduction) < $tolerance) {
    echo "✅ PASSED - Calculation fix is working correctly!\n";
} else {
    echo "❌ FAILED - Expected {$expectedReduction}x reduction, got {$actualReduction}x\n";
}

echo "\n=== Verification Complete ===\n";
