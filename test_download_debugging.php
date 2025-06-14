#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

// Simple test script to check download functionality
echo "=== Download Debugging Test ===\n";

// Test 1: Check if charts directory exists and is writable
$chartsDir = 'public/charts';
echo "\n1. Charts Directory Check:\n";
echo "   Directory: {$chartsDir}\n";
echo "   Exists: " . (is_dir($chartsDir) ? 'YES' : 'NO') . "\n";
echo "   Writable: " . (is_writable($chartsDir) ? 'YES' : 'NO') . "\n";

if (!is_dir($chartsDir)) {
    echo "   Creating directory...\n";
    mkdir($chartsDir, 0755, true);
    echo "   Created: " . (is_dir($chartsDir) ? 'YES' : 'NO') . "\n";
}

// Test 2: Check if any chart files exist
$existingFiles = glob("{$chartsDir}/*.png");
echo "\n2. Existing Chart Files:\n";
if (empty($existingFiles)) {
    echo "   No PNG files found in charts directory\n";
} else {
    foreach ($existingFiles as $file) {
        $size = filesize($file);
        $modified = filemtime($file);
        echo "   {$file} - Size: {$size} bytes, Modified: " . date('Y-m-d H:i:s', $modified) . "\n";
    }
}

// Test 3: Test API endpoint availability
echo "\n3. API Endpoint Test:\n";
$apiUrl = 'http://127.0.0.1:5001';
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'GET'
    ]
]);

$apiTest = @file_get_contents($apiUrl, false, $context);
echo "   API Base URL ({$apiUrl}): " . ($apiTest !== false ? 'REACHABLE' : 'UNREACHABLE') . "\n";

// Test 4: Check session storage directory
$sessionPath = 'storage/framework/sessions';
echo "\n4. Session Storage Check:\n";
echo "   Directory: {$sessionPath}\n";
echo "   Exists: " . (is_dir($sessionPath) ? 'YES' : 'NO') . "\n";
echo "   Writable: " . (is_writable($sessionPath) ? 'YES' : 'NO') . "\n";

// Test 5: Check if PDF template exists
$pdfTemplate = 'resources/views/plants/exports/pdf.blade.php';
echo "\n5. PDF Template Check:\n";
echo "   Template: {$pdfTemplate}\n";
echo "   Exists: " . (file_exists($pdfTemplate) ? 'YES' : 'NO') . "\n";

// Test 6: Check Laravel configuration
echo "\n6. Laravel Configuration:\n";
echo "   APP_ENV: " . (getenv('APP_ENV') ?: 'not set') . "\n";
echo "   APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'not set') . "\n";

echo "\n=== Test Complete ===\n";
