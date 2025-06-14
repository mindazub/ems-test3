<?php
/**
 * Test script to verify that CSV download buttons in dropdowns are now enabled
 */

echo "=== CSV DROPDOWN BUTTONS TEST ===\n";

$templateFile = '/var/www/ems/resources/views/plants/partials/plant-chart.blade.php';
$content = file_get_contents($templateFile);

// Check 1: No more conditional @if statements for CSV buttons
$conditionalBlocks = preg_match_all('/@if\(!empty\(\$plantId\)\)/', $content);
echo "1. Conditional @if blocks for plantId: " . ($conditionalBlocks === 0 ? "✓ REMOVED" : "✗ STILL PRESENT") . "\n";

// Check 2: CSV buttons have cursor-pointer class
$energyCSV = preg_match('/id="downloadCSV-energy".*cursor-pointer/', $content);
$batteryCSV = preg_match('/id="downloadCSV-battery".*cursor-pointer/', $content);
$savingsCSV = preg_match('/id="downloadCSV-savings".*cursor-pointer/', $content);

echo "2. Energy CSV button has cursor-pointer: " . ($energyCSV ? "✓ YES" : "✗ NO") . "\n";
echo "3. Battery CSV button has cursor-pointer: " . ($batteryCSV ? "✓ YES" : "✗ NO") . "\n";
echo "4. Savings CSV button has cursor-pointer: " . ($savingsCSV ? "✓ YES" : "✗ NO") . "\n";

// Check 3: No more "cursor-not-allowed" for CSV buttons
$disabledCSV = preg_match('/downloadCSV.*cursor-not-allowed/', $content);
echo "5. No cursor-not-allowed for CSV buttons: " . (!$disabledCSV ? "✓ YES" : "✗ STILL PRESENT") . "\n";

// Check 4: JavaScript handlers for CSV buttons exist
$energyHandler = preg_match('/downloadCSV-energy.*addEventListener/', $content);
$batteryHandler = preg_match('/downloadCSV-battery.*addEventListener/', $content);
$savingsHandler = preg_match('/downloadCSV-savings.*addEventListener/', $content);

echo "6. Energy CSV JavaScript handler: " . ($energyHandler ? "✓ YES" : "✗ NO") . "\n";
echo "7. Battery CSV JavaScript handler: " . ($batteryHandler ? "✓ YES" : "✗ NO") . "\n";
echo "8. Savings CSV JavaScript handler: " . ($savingsHandler ? "✓ YES" : "✗ NO") . "\n";

// Check 5: downloadCSVDirect function exists
$csvDirectFunction = preg_match('/function downloadCSVDirect/', $content);
echo "9. downloadCSVDirect function exists: " . ($csvDirectFunction ? "✓ YES" : "✗ NO") . "\n";

// Check 6: Plant ID detection function exists
$plantIdDetection = preg_match('/function detectPlantId/', $content);
echo "10. Plant ID detection function: " . ($plantIdDetection ? "✓ YES" : "✗ NO") . "\n";

echo "\n=== SUMMARY ===\n";

$allChecks = [
    $conditionalBlocks === 0,
    $energyCSV,
    $batteryCSV,
    $savingsCSV,
    !$disabledCSV,
    $energyHandler,
    $batteryHandler,
    $savingsHandler,
    $csvDirectFunction,
    $plantIdDetection
];

$passedChecks = array_sum($allChecks);
$totalChecks = count($allChecks);

echo "Passed: {$passedChecks}/{$totalChecks} checks\n";

if ($passedChecks === $totalChecks) {
    echo "✓ ALL TESTS PASSED - CSV dropdown buttons should now be enabled!\n";
} else {
    echo "✗ Some tests failed - further investigation needed\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Open the plant view in your browser: http://localhost:8000/plants/Planta_Jose\n";
echo "2. Check that all three chart dropdown buttons (Energy, Battery, Savings) have enabled 'Download CSV' options\n";
echo "3. Click on a 'Download CSV' button to test the download functionality\n";
echo "4. Check browser console for any JavaScript errors\n";

?>
