<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;

class DownloadController extends Controller
{
    public function download($plant, string $chart, string $type, Request $request)
    {
        try {
            // Get the selected date from request, default to today
            $selectedDate = $request->get('date', now()->format('Y-m-d'));
            
            Log::info("Download request", [
                'plant_id' => $plant,
                'chart' => $chart,
                'type' => $type,
                'date' => $selectedDate
            ]);

            switch ($type) {
                case 'png': return $this->downloadPNG($plant, $chart, $request);
                case 'csv': return $this->downloadCSV($plant, $chart, $selectedDate);
                case 'pdf': return $this->downloadPDF($plant, $chart, $request);
                default: abort(404);
            }
        } catch (\Exception $e) {
            Log::error("Download failed", [
                'plant_id' => $plant,
                'chart' => $chart,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    protected function downloadPNG($plantId, string $chart, Request $request)
    {
        $filename = "plant_{$plantId}_{$chart}_" . now()->format('Y-m-d_H-i-s') . ".png";
        
        // Get image data from request (sent from frontend)
        $imageData = $request->input('image_data');
        
        if (!$imageData) {
            // Fallback: try to find saved file - first try standard naming pattern
            $file = public_path("charts/{$plantId}_{$chart}.png");
            
            if (!file_exists($file)) {
                // If standard file doesn't exist, look for the most recent timestamped file
                $chartsDir = public_path("charts");
                if (is_dir($chartsDir)) {
                    $pattern = "{$plantId}_{$chart}_*.png";
                    $files = glob("{$chartsDir}/{$pattern}");
                    if (!empty($files)) {
                        // Get the most recent file
                        usort($files, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        $file = $files[0];
                    } else {
                        return back()->with('error', 'Chart image not found. Please try again.');
                    }
                } else {
                    return back()->with('error', 'Chart image not found. Please try again.');
                }
            }
            
            return response()->download($file, $filename);
        }
        
        // Process base64 image data
        if (preg_match('/^data:image\/png;base64,/', $imageData)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $imageData = base64_decode($imageData);
            
            return response($imageData, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($imageData)
            ]);
        }
        
        return back()->with('error', 'Invalid image data format.');
    }

    protected function downloadCSV($plantId, string $chart, string $selectedDate)
    {
        // Get dynamic data from the plant's API
        $data = $this->getPlantChartData($plantId, $selectedDate);
        
        if (empty($data)) {
            return back()->with('error', 'No data available for the selected date.');
        }
        
        $chartData = match ($chart) {
            'energy' => $data['energy_chart'] ?? [],
            'battery' => $data['battery_price'] ?? [],
            'savings' => $data['battery_savings'] ?? [],
            default => []
        };
        
        if (empty($chartData)) {
            return back()->with('error', "No {$chart} data available for {$selectedDate}.");
        }
        
        $filename = "plant_{$plantId}_{$chart}_{$selectedDate}.csv";
        
        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Cache-Control" => "no-cache, no-store, must-revalidate",
            "Pragma" => "no-cache",
            "Expires" => "0"
        ];

        $callback = function () use ($chartData, $chart) {
            $out = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 support in Excel
            fwrite($out, "\xEF\xBB\xBF");

            // Write headers based on chart type
            match ($chart) {
                'energy' => fputcsv($out, ['Timestamp', 'Time', 'PV Power (kW)', 'Battery Power (kW)', 'Grid Power (kW)', 'Load Power (kW)']),
                'battery' => fputcsv($out, ['Timestamp', 'Time', 'Battery Power (kW)', 'Energy Price (€/kWh)', 'Price (€/kWh)']),
                'savings' => fputcsv($out, ['Timestamp', 'Time', 'Battery Savings (€)']),
            };

            // Sort data by timestamp
            uksort($chartData, function($a, $b) {
                return strtotime($a) - strtotime($b);
            });

            foreach ($chartData as $timestamp => $values) {
                $formattedTime = date('Y-m-d H:i:s', strtotime($timestamp));
                $timeOnly = date('H:i', strtotime($timestamp));
                
                match ($chart) {
                    'energy' => fputcsv($out, [
                        $timestamp,
                        $timeOnly,
                        round(($values['pv_p'] ?? 0) / 1000, 3),
                        round(($values['battery_p'] ?? 0) / 1000, 3),
                        round(($values['grid_p'] ?? 0) / 1000, 3),
                        round(($values['load_p'] ?? 0) / 1000, 3)
                    ]),
                    'battery' => fputcsv($out, [
                        $timestamp,
                        $timeOnly,
                        round(($values['battery_p'] ?? 0) / 1000, 3),
                        round($values['tariff'] ?? 0, 4),
                        round($values['price'] ?? ($values['tariff'] ?? 0), 4)
                    ]),
                    'savings' => fputcsv($out, [
                        $timestamp,
                        $timeOnly,
                        round($values['battery_savings'] ?? 0, 4)
                    ]),
                };
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function downloadPDF($plantId, string $chart, Request $request)
    {
        Log::info("=== PDF DOWNLOAD DEBUG START ===", [
            'plant_id' => $plantId,
            'chart' => $chart,
            'request_data' => $request->all()
        ]);
        
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $imageData = $request->input('image_data');
        
        Log::info("PDF download parameters", [
            'selected_date' => $selectedDate,
            'has_image_data' => !empty($imageData),
            'image_data_length' => $imageData ? strlen($imageData) : 0
        ]);
        
        // Get plant data from API for PDF metadata
        try {
            $plant = $this->fetchPlantFromAPI($plantId);
            if (!$plant) {
                throw new \Exception("Plant not found in API");
            }
            Log::info("Plant found", ['plant_name' => $plant->name ?? $plant->uid]);
        } catch (\Exception $e) {
            Log::error("Plant not found", ['plant_id' => $plantId, 'error' => $e->getMessage()]);
            throw $e;
        }
        
        // Get dynamic data
        try {
            $data = $this->getPlantChartData($plantId, $selectedDate);
            Log::info("Chart data retrieved", ['data_keys' => array_keys($data), 'data_count' => count($data)]);
        } catch (\Exception $e) {
            Log::error("Failed to get chart data", ['error' => $e->getMessage()]);
            throw $e;
        }
        
        $chartData = match ($chart) {
            'energy' => $data['energy_chart'] ?? [],
            'battery' => $data['battery_price'] ?? [],
            'savings' => $data['battery_savings'] ?? [],
            default => []
        };
        
        Log::info("Chart data extracted", [
            'chart_type' => $chart,
            'data_count' => count($chartData)
        ]);
        
        // Process image for PDF
        $chartImage = null;
        if ($imageData && preg_match('/^data:image\/png;base64,/', $imageData)) {
            $chartImage = $imageData;
            Log::info("Using provided image data for PDF");
        } else {
            Log::info("No image data provided, looking for saved file");
            
            // Fallback to saved file - first try the standard naming pattern
            $imagePath = public_path("charts/{$plantId}_{$chart}.png");
            Log::info("Checking for standard image file", ['path' => $imagePath]);
            
            if (file_exists($imagePath)) {
                $imageContent = base64_encode(file_get_contents($imagePath));
                $chartImage = 'data:image/png;base64,' . $imageContent;
                Log::info("Found standard image file", ['size' => filesize($imagePath)]);
            } else {
                Log::info("Standard file not found, searching for timestamped files");
                
                // If standard file doesn't exist, look for the most recent timestamped file
                $chartsDir = public_path("charts");
                if (is_dir($chartsDir)) {
                    $pattern = "{$plantId}_{$chart}_*.png";
                    $files = glob("{$chartsDir}/{$pattern}");
                    Log::info("Searching for files", ['pattern' => $pattern, 'found_count' => count($files)]);
                    
                    if (!empty($files)) {
                        // Get the most recent file
                        usort($files, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        $imagePath = $files[0];
                        $imageContent = base64_encode(file_get_contents($imagePath));
                        $chartImage = 'data:image/png;base64,' . $imageContent;
                        Log::info("Found timestamped image file", ['path' => $imagePath, 'size' => filesize($imagePath)]);
                    } else {
                        Log::warning("No image files found for PDF generation");
                    }
                } else {
                    Log::error("Charts directory does not exist", ['dir' => $chartsDir]);
                }
            }
        }
        
        $plantName = $plant->name ?? $plant->uid ?? 'unknown_plant';
        $filename = "{$plantName}_{$chart}_{$selectedDate}.pdf";
        
        // Calculate summary statistics
        $summary = $this->calculateChartSummary($chartData, $chart);
        
        Log::info("Generating PDF", [
            'filename' => $filename,
            'has_chart_image' => !empty($chartImage),
            'chart_data_count' => count($chartData),
            'summary' => $summary
        ]);
        
        try {
            $pdf = PDF::loadView("plants.exports.pdf", [
                'plant' => $plant,
                'chart' => $chart,
                'chartImage' => $chartImage,
                'chartData' => $chartData,
                'selectedDate' => $selectedDate,
                'summary' => $summary,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
                'user' => $request->user() // Add user for time format preference
            ])->setPaper('a4');
            
            Log::info("PDF generated successfully", ['filename' => $filename]);
            
            $pdfOutput = $pdf->download($filename);
            Log::info("=== PDF DOWNLOAD DEBUG END ===");
            
            return $pdfOutput;
        } catch (\Exception $e) {
            Log::error("PDF generation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get plant chart data from API using the same pattern as PlantController
     */
    private function getPlantChartData($plantId, string $selectedDate)
    {
        try {
            Log::info("Getting plant chart data from API", [
                'plant_id' => $plantId,
                'selected_date' => $selectedDate
            ]);
            
            // Parse the selected date
            $date = Carbon::createFromFormat('Y-m-d', $selectedDate);
            
            // Calculate timestamps for the selected date in EEST timezone
            $startOfDay = $date->copy()->startOfDay()->subHours(3)->timestamp; // Convert to UTC
            $endOfDay = $date->copy()->endOfDay()->subHours(3)->timestamp;
            
            Log::info("Calculated timestamps", [
                'start_timestamp' => $startOfDay,
                'end_timestamp' => $endOfDay,
                'start_date' => date('Y-m-d H:i:s', $startOfDay),
                'end_date' => date('Y-m-d H:i:s', $endOfDay)
            ]);
            
            // Use the same API call pattern as PlantController
            $client = new Client();
            $url = "http://127.0.0.1:5001/plant_view/{$plantId}?start={$startOfDay}&end={$endOfDay}";
            $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
            
            Log::info("Making API request", ['url' => $url]);
            
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info("API response received", [
                'data_keys' => array_keys($data ?? []),
                'snapshots_count' => count($data['aggregated_data_snapshots'] ?? [])
            ]);
            
            // Process the data and format for charts using the same formatDataForCharts method
            $result = $this->formatDataForCharts($data);
            
            Log::info("Formatted chart data", [
                'result_keys' => array_keys($result),
                'energy_chart_count' => count($result['energy_chart'] ?? []),
                'battery_price_count' => count($result['battery_price'] ?? []),
                'battery_savings_count' => count($result['battery_savings'] ?? [])
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("Failed to get plant data for download", [
                'plant_id' => $plantId,
                'date' => $selectedDate,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [];
        }
    }
    
    /**
     * Format data for chart display (same as PlantController)
     */
    private function formatDataForCharts($data)
    {
        $result = [
            'energy_chart' => [],
            'battery_price' => [],
            'battery_savings' => []
        ];
        
        // Process raw data into chart format
        $aggregatedSnapshots = $data['aggregated_data_snapshots'] ?? [];
        
        Log::info("Processing aggregated snapshots", ['count' => count($aggregatedSnapshots)]);
        
        // Calculate the actual time interval between data points
        $timeIntervalHours = $this->calculateTimeInterval($aggregatedSnapshots);
        
        Log::info("Using time interval for battery savings", [
            'time_interval_hours' => $timeIntervalHours,
            'time_interval_minutes' => $timeIntervalHours * 60
        ]);
        
        foreach ($aggregatedSnapshots as $snapshot) {
            // Convert snapshot to object if it's an array
            if (is_array($snapshot)) {
                $snapshot = (object) $snapshot;
            }
            
            // Use timestamp or dt (convert to ISO string)
            $timestamp = null;
            if (!empty($snapshot->timestamp)) {
                $timestamp = $snapshot->timestamp;
            } elseif (!empty($snapshot->dt)) {
                // Convert Unix timestamp to ISO string
                $timestamp = date('c', $snapshot->dt);
            }

            if ($timestamp) {
                // Energy chart data - now includes load_p
                $result['energy_chart'][$timestamp] = [
                    'pv_p' => $snapshot->pv_p ?? 0,
                    'battery_p' => $snapshot->battery_p ?? 0,
                    'grid_p' => $snapshot->grid_p ?? 0,
                    'load_p' => $snapshot->load_p ?? 0  // Add load power from API
                ];
                
                // Battery price data - now includes both tariff and price
                $result['battery_price'][$timestamp] = [
                    'battery_p' => $snapshot->battery_p ?? 0,
                    'tariff' => $snapshot->tariff ?? 0.15, // Default tariff if missing
                    'price' => $snapshot->price ?? ($snapshot->tariff ?? 0.15) // Use price if available, fallback to tariff
                ];
                
                // Battery savings data
                $batterySavings = $snapshot->battery_savings ?? null;
                
                // Calculate savings if missing but battery power and tariff are available
                if ($batterySavings === null && isset($snapshot->battery_p)) {
                    $batteryPower = floatval($snapshot->battery_p);
                    $tariff = floatval($snapshot->tariff ?? 0.15);
                    
                    // Battery discharging (positive) means saving money
                    if ($batteryPower > 0) {
                        // Convert W to kW and multiply by the actual time interval
                        // This prevents over-inflating the total daily savings
                        $batterySavings = ($batteryPower / 1000) * $tariff * $timeIntervalHours;
                    } else {
                        $batterySavings = 0; // No savings when charging
                    }
                }
                
                if ($batterySavings !== null) {
                    Log::info("BATTERY SAVINGS - Individual calculation", [
                        'timestamp' => $timestamp,
                        'battery_power' => $batteryPower,
                        'tariff' => $tariff,
                        'time_interval' => $timeIntervalHours,
                        'calculated_savings' => $batterySavings
                    ]);
                    
                    $result['battery_savings'][$timestamp] = [
                        'battery_savings' => $batterySavings,
                        'tariff' => $snapshot->tariff ?? 0.15
                    ];
                }
            }
        }
        
        Log::info("Chart data formatting complete", [
            'energy_chart_count' => count($result['energy_chart']),
            'battery_price_count' => count($result['battery_price']),
            'battery_savings_count' => count($result['battery_savings'])
        ]);
        
        return $result;
    }
    
    /**
     * Calculate summary statistics for charts
     */
    private function calculateChartSummary(array $chartData, string $chart): array
    {
        if (empty($chartData)) {
            return [];
        }
        
        $summary = [];
        
        switch ($chart) {
            case 'energy':
                $pvSum = $batterySum = $gridSum = $loadSum = 0;
                $count = 0;
                
                foreach ($chartData as $values) {
                    $pvSum += ($values['pv_p'] ?? 0) / 1000;
                    $batterySum += ($values['battery_p'] ?? 0) / 1000;
                    $gridSum += ($values['grid_p'] ?? 0) / 1000;
                    $loadSum += ($values['load_p'] ?? 0) / 1000;  // Add load power sum
                    $count++;
                }
                
                if ($count > 0) {
                    $summary = [
                        'avg_pv' => round($pvSum / $count, 2),
                        'avg_battery' => round($batterySum / $count, 2),
                        'avg_grid' => round($gridSum / $count, 2),
                        'avg_load' => round($loadSum / $count, 2),  // Add average load
                        'total_pv' => round($pvSum, 2),
                        'total_load' => round($loadSum, 2),  // Add total load
                        'data_points' => $count
                    ];
                }
                break;
                
            case 'battery':
                $powerSum = $tariffSum = $priceSum = 0;
                $count = 0;
                $tariffValues = [];
                $priceValues = [];
                
                foreach ($chartData as $values) {
                    $powerSum += ($values['battery_p'] ?? 0) / 1000;
                    $tariff = $values['tariff'] ?? 0;
                    $price = $values['price'] ?? $tariff;  // Use price if available, fallback to tariff
                    
                    $tariffSum += $tariff;
                    $priceSum += $price;
                    $tariffValues[] = $tariff;
                    $priceValues[] = $price;
                    $count++;
                }
                
                if ($count > 0) {
                    $summary = [
                        'avg_power' => round($powerSum / $count, 2),
                        'avg_tariff' => round($tariffSum / $count, 4),
                        'avg_price' => round($priceSum / $count, 4),  // Add average price
                        'max_tariff' => round(max($tariffValues), 4),
                        'min_tariff' => round(min($tariffValues), 4),
                        'max_price' => round(max($priceValues), 4),  // Add max price
                        'min_price' => round(min($priceValues), 4),  // Add min price
                        'data_points' => $count
                    ];
                }
                break;
                
            case 'savings':
                $totalSavings = 0;
                $count = 0;
                
                foreach ($chartData as $values) {
                    $totalSavings += $values['battery_savings'] ?? 0;
                    $count++;
                }
                
                $summary = [
                    'total_savings' => round($totalSavings, 2),
                    'avg_savings' => $count > 0 ? round($totalSavings / $count, 4) : 0,
                    'data_points' => $count
                ];
                break;
        }
        
        return $summary;
    }

    public function saveChartImage(Request $request, $plant)
    {
        Log::info("=== SAVE CHART IMAGE DEBUG START ===", [
            'plant_id' => $plant,
            'request_data' => $request->except('image') // Don't log the full image data
        ]);
        
        try {
            $chart = $request->input('chart'); // 'energy', 'battery', or 'savings'
            $imageData = $request->input('image');

            Log::info("Save chart parameters", [
                'chart' => $chart,
                'has_image_data' => !empty($imageData),
                'image_data_length' => $imageData ? strlen($imageData) : 0
            ]);

            if (!$chart || !$imageData) {
                Log::error("Missing chart or image data", ['chart' => $chart, 'has_image' => !empty($imageData)]);
                return response()->json(['success' => false, 'message' => 'Missing chart or image data'], 400);
            }

            // Validate chart type
            if (!in_array($chart, ['energy', 'battery', 'savings'])) {
                Log::error("Invalid chart type", ['chart' => $chart]);
                return response()->json(['success' => false, 'message' => 'Invalid chart type'], 400);
            }

            // Check and decode base64 image
            if (preg_match('/^data:image\/png;base64,/', $imageData)) {
                $imageContent = substr($imageData, strpos($imageData, ',') + 1);
                $decodedImage = base64_decode($imageContent);
                
                Log::info("Image data validated", ['decoded_size' => strlen($decodedImage)]);
                
                if ($decodedImage === false) {
                    Log::error("Failed to decode base64 image");
                    return response()->json(['success' => false, 'message' => 'Invalid base64 image'], 400);
                }
                
                // Ensure charts directory exists
                $dir = public_path("charts");
                if (!is_dir($dir)) {
                    Log::info("Creating charts directory", ['dir' => $dir]);
                    mkdir($dir, 0755, true);
                }
                
                $filename = "{$plant}_{$chart}.png";
                $path = "{$dir}/{$filename}";
                
                Log::info("Saving image file", ['path' => $path, 'filename' => $filename]);
                
                $result = file_put_contents($path, $decodedImage);
                
                if ($result === false) {
                    Log::error("Failed to save chart image", [
                        'plant_id' => $plant,
                        'chart' => $chart,
                        'path' => $path
                    ]);
                    return response()->json(['success' => false, 'message' => 'Failed to save image'], 500);
                }
                
                Log::info("Chart image saved successfully", [
                    'plant_id' => $plant,
                    'chart' => $chart,
                    'filename' => $filename,
                    'size' => $result
                ]);
                
                Log::info("=== SAVE CHART IMAGE DEBUG END ===");
                
                return response()->json([
                    'success' => true, 
                    'filename' => $filename,
                    'path' => $path
                ]);
            }
            
            Log::error("Invalid image format", ['image_prefix' => substr($imageData, 0, 50)]);
            return response()->json(['success' => false, 'message' => 'Invalid image format'], 400);
            
        } catch (\Exception $e) {
            Log::error("Exception in saveChartImage", [
                'plant_id' => $plant,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
    
    /**
     * Download comprehensive plant report as PDF with charts and data
     */
    public function downloadPlantReport($plantId, Request $request)
    {
        try {
            Log::info("Generating comprehensive plant report with charts", ['plant_id' => $plantId]);
            
            $selectedDate = $request->get('date', now()->format('Y-m-d'));
            
            // Fetch plant data from API (same as show method)
            $plant = $this->fetchPlantFromAPI($plantId);
            
            if (!$plant) {
                return back()->with('error', 'Plant not found or API unavailable.');
            }
            
            // Get REAL chart data from API like the show page does
            $realData = $this->getPlantChartData($plantId, $selectedDate);
            
            Log::info("PDF Generation - Real Data Retrieved", [
                'plant_id' => $plantId,
                'date' => $selectedDate,
                'data_keys' => array_keys($realData),
                'energy_chart_count' => isset($realData['energy_chart']) ? count($realData['energy_chart']) : 0,
                'battery_price_count' => isset($realData['battery_price']) ? count($realData['battery_price']) : 0,
                'battery_savings_count' => isset($realData['battery_savings']) ? count($realData['battery_savings']) : 0,
                'energy_chart_sample' => isset($realData['energy_chart']) ? array_slice($realData['energy_chart'], 0, 2, true) : 'none',
            ]);
            
            // Get chart images from session if available
            $chartImages = session()->get("chart_images_{$plantId}_{$selectedDate}", []);
            
            Log::info("Chart images from session", [
                'plant_id' => $plantId,
                'date' => $selectedDate,
                'session_key' => "chart_images_{$plantId}_{$selectedDate}",
                'images_found' => !empty($chartImages),
                'image_keys' => array_keys($chartImages),
                'image_sizes' => array_map(function($img) {
                    return is_string($img) ? strlen($img) : 'not_string';
                }, $chartImages)
            ]);
            
            // If no images in session, try to get them from request or use placeholders
            if (empty($chartImages)) {
                Log::warning("No chart images found in session for PDF generation", [
                    'plant_id' => $plantId,
                    'date' => $selectedDate,
                    'all_session_keys' => array_keys(session()->all())
                ]);
                
                // Try to load from saved files as fallback
                $chartImages = [];
                $chartTypes = ['energy', 'battery', 'savings'];
                foreach ($chartTypes as $chartType) {
                    $imagePath = public_path("charts/{$plantId}_{$chartType}.png");
                    if (file_exists($imagePath)) {
                        $imageContent = base64_encode(file_get_contents($imagePath));
                        $chartImages[$chartType] = 'data:image/png;base64,' . $imageContent;
                        Log::info("Loaded fallback image from file", [
                            'chart_type' => $chartType,
                            'path' => $imagePath,
                            'size' => filesize($imagePath)
                        ]);
                    }
                }
            } else {
                Log::info("Chart images loaded from session successfully", [
                    'images_count' => count($chartImages),
                    'total_size' => array_sum(array_map('strlen', $chartImages))
                ]);
            }
            
            // Transform data to match template format
            $energyData = [];
            $batteryData = [];
            $savingsData = [];
            $temperatureData = [];
            
            Log::info("Starting data transformation for PDF", [
                'realData_structure' => array_map(function($item) {
                    return is_array($item) ? count($item) . ' items' : gettype($item);
                }, $realData)
            ]);
            
            // Transform energy chart data for Energy Live Table: Time | PV (kW) | Battery (kW) | Grid (kW) | Load (kW)
            if (!empty($realData['energy_chart'])) {
                Log::info("Transforming energy data", ['count' => count($realData['energy_chart'])]);
                foreach ($realData['energy_chart'] as $timestamp => $values) {
                    $energyData[] = [
                        'time' => date('H:i', strtotime($timestamp)),
                        'pv_power' => ($values['pv_p'] ?? 0), // Keep in watts, will convert to kW in template
                        'battery_power' => ($values['battery_p'] ?? 0), // Keep in watts
                        'grid_power' => ($values['grid_p'] ?? 0), // Keep in watts, use grid_p for Grid column
                        'load_power' => ($values['load_p'] ?? 0), // Keep in watts, will convert to kW in template
                    ];
                }
                Log::info("Energy data transformed", ['result_count' => count($energyData)]);
            }
            
            // Transform battery data for Battery Power and Energy Price Table: Time | Battery Power (kW) | Energy Price (€/kWh) | Price (€/kWh)
            if (!empty($realData['battery_price'])) {
                Log::info("Transforming battery data", ['count' => count($realData['battery_price'])]);
                foreach ($realData['battery_price'] as $timestamp => $values) {
                    $batteryData[] = [
                        'time' => date('H:i', strtotime($timestamp)),
                        'battery_power' => ($values['battery_p'] ?? 0), // Keep in watts, will convert to kW in template
                        'energy_price' => ($values['tariff'] ?? 0.1500), // Energy price in €/kWh
                        'price' => ($values['price'] ?? ($values['tariff'] ?? 0.1500)), // Price field, fallback to tariff
                    ];
                }
                Log::info("Battery data transformed", ['result_count' => count($batteryData)]);
            }
            
            // Transform savings data for Battery Savings Table: Time | Battery Savings (€)
            if (!empty($realData['battery_savings'])) {
                Log::info("Transforming savings data", ['count' => count($realData['battery_savings'])]);
                foreach ($realData['battery_savings'] as $timestamp => $values) {
                    $savingsData[] = [
                        'time' => date('H:i', strtotime($timestamp)),
                        'savings' => ($values['battery_savings'] ?? 0), // Battery savings in euros
                    ];
                }
                Log::info("Savings data transformed", ['result_count' => count($savingsData)]);
            }
            
            // Prepare metadata from API plant data
            $metadata = [];
            if ($plant) {
                $metadata['Plant Name'] = $plant->name ?? $plant->uid ?? 'Unknown';
                $metadata['Plant ID'] = $plant->uid ?? $plantId;
                $metadata['Status'] = $plant->status ?? 'Unknown';
                $metadata['Owner'] = $plant->owner_name ?? $plant->owner_email ?? 'Unknown';
                $metadata['Device Count'] = $plant->device_amount ?? 0;
                $metadata['Last Updated'] = $plant->last_updated ?? 'Unknown';
                
                // Add coordinates if available
                if (isset($plant->latitude) && isset($plant->longitude)) {
                    $metadata['Latitude'] = $plant->latitude;
                    $metadata['Longitude'] = $plant->longitude;
                }
                
                // Add capacity if available
                if (isset($plant->capacity)) {
                    $metadata['Capacity'] = $plant->capacity;
                }
            }
            
            // Prepare data for PDF template
            $data = [
                'plant' => $plant,
                'id' => $plantId,
                'selectedDate' => $selectedDate,
                'chartData' => $realData,
                'energyData' => $energyData,
                'batteryData' => $batteryData,
                'savingsData' => $savingsData,
                'temperatureData' => $temperatureData, // Empty for now
                'chartImages' => $chartImages,
                'user' => $request->user(), // Include user for time format
                'generatedAt' => now()->format('Y-m-d H:i:s'),
                'metadata' => $metadata,
            ];
            
            Log::info("Final PDF data prepared", [
                'energyData_count' => count($energyData),
                'batteryData_count' => count($batteryData),
                'savingsData_count' => count($savingsData),
                'metadata_keys' => array_keys($metadata),
                'chartImages_keys' => array_keys($chartImages),
            ]);

            // Generate PDF using chart+data template
            $pdf = PDF::loadView('plants.exports.chart-data-report', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'defaultFont' => 'DejaVu Sans',
                    'dpi' => 150,
                    'debugKeepTemp' => false,
                    'isRemoteEnabled' => true
                ]);

            $filename = "plant_report_{$plantId}_" . now()->format('Y-m-d_H-i-s') . ".pdf";
            
            Log::info("Plant report with charts generated successfully", ['filename' => $filename]);
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error("Plant report generation failed", [
                'plant_id' => $plantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to generate plant report: ' . $e->getMessage());
        }
    }

    /**
     * Download all chart images as ZIP
     */
    public function downloadAllCharts($plantId, Request $request)
    {
        try {
            Log::info("Generating all charts ZIP", ['plant_id' => $plantId]);
            
            $selectedDate = $request->get('date', now()->format('Y-m-d'));
            
            // Get saved chart images from session
            $chartImages = session()->get("chart_images_{$plantId}_{$selectedDate}", []);
            
            if (empty($chartImages)) {
                Log::error("No chart images found in session", [
                    'plant_id' => $plantId,
                    'date' => $selectedDate,
                    'session_key' => "chart_images_{$plantId}_{$selectedDate}"
                ]);
                return back()->with('error', 'No chart images found. Please try again.');
            }
            
            // Create temporary ZIP file
            $zipFilename = "plant_charts_{$plantId}_" . now()->format('Y-m-d_H-i-s') . ".zip";
            $zipPath = storage_path('app/temp/' . $zipFilename);
            
            // Ensure temp directory exists
            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }
            
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception("Cannot create ZIP file");
            }
            
            // Add each chart image to ZIP
            foreach ($chartImages as $chartType => $imageData) {
                if (strpos($imageData, 'data:image/') === 0) {
                    // Remove data URL prefix
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $decodedImage = base64_decode($imageData);
                    
                    if ($decodedImage) {
                        $imageFilename = "plant_{$plantId}_{$chartType}_{$selectedDate}.png";
                        $zip->addFromString($imageFilename, $decodedImage);
                        Log::info("Added chart to ZIP", ['chart' => $chartType, 'filename' => $imageFilename]);
                    }
                }
            }
            
            $zip->close();
            
            // Clear session data
            session()->forget("chart_images_{$plantId}_{$selectedDate}");
            
            Log::info("Charts ZIP created successfully", ['zip_path' => $zipPath]);
            
            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error("Charts ZIP generation failed", [
                'plant_id' => $plantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to generate charts ZIP: ' . $e->getMessage());
        }
    }

    /**
     * Download all CSV data as ZIP
     */
    public function downloadAllCSV($plantId, Request $request)
    {
        try {
            Log::info("Generating all CSV ZIP", ['plant_id' => $plantId]);
            
            $selectedDate = $request->get('date', now()->format('Y-m-d'));
            
            // Get saved chart data from session
            $chartData = session()->get("chart_data_{$plantId}_{$selectedDate}", []);
            
            if (empty($chartData)) {
                Log::error("No chart data found in session", [
                    'plant_id' => $plantId,
                    'date' => $selectedDate,
                    'session_key' => "chart_data_{$plantId}_{$selectedDate}"
                ]);
                return back()->with('error', 'No chart data found. Please try again.');
            }
            
            // Create temporary ZIP file
            $zipFilename = "plant_csv_data_{$plantId}_" . now()->format('Y-m-d_H-i-s') . ".zip";
            $zipPath = storage_path('app/temp/' . $zipFilename);
            
            // Ensure temp directory exists
            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }
            
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception("Cannot create ZIP file");
            }
            
            // Process each chart type
            foreach ($chartData as $chartType => $data) {
                try {
                    if (!empty($data['labels']) && !empty($data['datasets'])) {
                        $csvContent = $this->generateCSVFromChartData($data, $chartType);
                        $csvFilename = "plant_{$plantId}_{$chartType}_{$selectedDate}.csv";
                        $zip->addFromString($csvFilename, $csvContent);
                        Log::info("Added CSV to ZIP", ['chart' => $chartType, 'filename' => $csvFilename]);
                    }
                } catch (\Exception $e) {
                    Log::warning("Skipping chart type due to error", [
                        'chart_type' => $chartType, 
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $zip->close();
            
            // Clear session data
            session()->forget("chart_data_{$plantId}_{$selectedDate}");
            
            Log::info("CSV ZIP created successfully", ['zip_path' => $zipPath]);
            
            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error("CSV ZIP generation failed", [
                'plant_id' => $plantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to generate CSV ZIP: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to generate CSV content from chart data
     */
    private function generateCSVContent($data, $chartType)
    {
        $csv = "Time,Value,Unit\n";
        
        foreach ($data as $point) {
            $time = $point['time'] ?? '';
            $value = $point['value'] ?? 0;
            $unit = $this->getUnitForChartType($chartType);
            
            $csv .= "{$time},{$value},{$unit}\n";
        }
        
        return $csv;
    }

    /**
     * Helper method to generate CSV content from Chart.js data
     */
    private function generateCSVFromChartData($chartData, $chartType)
    {
        Log::info("Generating CSV from chart data", [
            'chart_type' => $chartType,
            'labels_count' => count($chartData['labels'] ?? []),
            'datasets_count' => count($chartData['datasets'] ?? [])
        ]);

        $labels = $chartData['labels'] ?? [];
        $datasets = $chartData['datasets'] ?? [];
        
        if (empty($labels) || empty($datasets)) {
            Log::warning("Empty labels or datasets", ['labels' => count($labels), 'datasets' => count($datasets)]);
            return "No data available\n";
        }

        // Create CSV header based on chart type
        $header = ['Time'];
        foreach ($datasets as $dataset) {
            $header[] = $dataset['label'] ?? 'Value';
        }
        
        $csv = implode(',', $header) . "\n";
        
        // Add data rows
        foreach ($labels as $index => $label) {
            $row = [$label];
            
            foreach ($datasets as $dataset) {
                $value = $dataset['data'][$index] ?? 0;
                $row[] = is_numeric($value) ? round($value, 3) : $value;
            }
            
            $csv .= implode(',', $row) . "\n";
        }
        
        Log::info("CSV generated successfully", [
            'chart_type' => $chartType,
            'rows' => count($labels),
            'columns' => count($header)
        ]);
        
        return $csv;
    }

    /**
     * Helper method to get unit for chart type
     */
    private function getUnitForChartType($chartType)
    {
        switch ($chartType) {
            case 'energy': return 'kWh';
            case 'battery': return '%';
            case 'savings': return '$';
            default: return '';
        }
    }

    /**
     * Helper method to get chart data (you may need to adjust this based on your existing data retrieval logic)
     */
    private function getChartData($plantId, $chartType, $selectedDate)
    {
        // This should match your existing chart data retrieval logic
        // For now, returning sample data - replace with your actual implementation
        
        $sampleData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $time = sprintf('%02d:00', $hour);
            $value = rand(10, 100);
            $sampleData[] = ['time' => $time, 'value' => $value];
        }
        
        return $sampleData;
    }

    /**
     * Save multiple chart images for bulk download
     */
    public function saveChartImages($plantId, Request $request)
    {
        try {
            $chartImages = $request->input('chart_images', []);
            $date = $request->input('date', now()->format('Y-m-d'));
            
            Log::info("Saving chart images for PDF generation", [
                'plant_id' => $plantId,
                'date' => $date,
                'images_count' => count($chartImages),
                'image_keys' => array_keys($chartImages),
                'image_sizes' => array_map(function($img) {
                    return is_string($img) ? strlen($img) : 'not_string';
                }, $chartImages)
            ]);
            
            // Validate that we have valid base64 image data
            $validImages = [];
            foreach ($chartImages as $chartType => $imageData) {
                if (is_string($imageData) && str_starts_with($imageData, 'data:image/')) {
                    $validImages[$chartType] = $imageData;
                    Log::info("Valid image data for chart", [
                        'chart_type' => $chartType,
                        'data_length' => strlen($imageData)
                    ]);
                } else {
                    Log::warning("Invalid image data for chart", [
                        'chart_type' => $chartType,
                        'data_type' => gettype($imageData),
                        'data_preview' => is_string($imageData) ? substr($imageData, 0, 50) : 'not_string'
                    ]);
                }
            }
            
            if (empty($validImages)) {
                Log::error("No valid chart images to save");
                return response()->json(['success' => false, 'message' => 'No valid chart images provided'], 400);
            }
            
            // Store images temporarily
            $sessionKey = "chart_images_{$plantId}_{$date}";
            session()->put($sessionKey, $validImages);
            
            Log::info("Chart images saved to session", [
                'session_key' => $sessionKey,
                'valid_images_count' => count($validImages),
                'session_stored' => session()->has($sessionKey)
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Chart images saved successfully',
                'images_saved' => count($validImages),
                'chart_types' => array_keys($validImages)
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to save chart images", [
                'plant_id' => $plantId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Failed to save chart images'], 500);
        }
    }

    /**
     * Save chart data for bulk CSV download
     */
    public function saveChartData($plantId, Request $request)
    {
        try {
            $chartData = $request->input('chart_data', []);
            $date = $request->input('date', now()->format('Y-m-d'));
            
            Log::info("Saving chart data for bulk download", [
                'plant_id' => $plantId,
                'date' => $date,
                'charts_count' => count($chartData)
            ]);
            
            // Store data temporarily
            session()->put("chart_data_{$plantId}_{$date}", $chartData);
            
            return response()->json(['success' => true, 'message' => 'Chart data saved successfully']);
            
        } catch (\Exception $e) {
            Log::error("Failed to save chart data", [
                'plant_id' => $plantId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Failed to save chart data'], 500);
        }
    }
    
    /**
     * Fetch plant data from API (same logic as PlantController)
     */
    private function fetchPlantFromAPI($plantId)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $url = "http://127.0.0.1:5001/plant_view/{$plantId}";
            $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
            
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);
            
            $plant = json_decode($response->getBody()->getContents(), false);
            
            // Add the specific owner info if plant was successfully loaded
            $specificOwnerUuid = '6a36660d-daae-48dd-a4fe-000b191b13d8';
            $specificOwner = \App\Models\User::where('uuid', $specificOwnerUuid)->first();
            
            if ($plant && $specificOwner) {
                $plant->owner_name = $specificOwner->name;
                $plant->owner_email = $specificOwner->email;
                $plant->owner_uuid = $specificOwner->uuid;
            }
            
            // Normalize plant object
            if ($plant) {
                $plant->uid = $plant->uid ?? null;
                $plant->uuid = $plant->uuid ?? $plant->uid ?? null;
                $plant->name = $plant->name ?? $plant->uid ?? 'Unknown Plant';
            }
            
            Log::info("Plant fetched from API successfully", [
                'plant_id' => $plantId,
                'plant_name' => $plant->name ?? 'Unknown',
                'plant_uid' => $plant->uid ?? 'Unknown'
            ]);
            
            return $plant;
            
        } catch (\Exception $e) {
            Log::error("Failed to fetch plant from API", [
                'plant_id' => $plantId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Calculate the time interval in hours between data points
     * This helps ensure accurate battery savings calculations
     */
    private function calculateTimeInterval($aggregatedSnapshots): float
    {
        if (count($aggregatedSnapshots) < 2) {
            return 0.5; // Default to 30 minutes if we can't calculate
        }
        
        $timestamps = [];
        foreach ($aggregatedSnapshots as $snapshot) {
            if (is_array($snapshot)) {
                $snapshot = (object) $snapshot;
            }
            
            if (!empty($snapshot->timestamp)) {
                $timestamps[] = strtotime($snapshot->timestamp);
            } elseif (!empty($snapshot->dt)) {
                $timestamps[] = $snapshot->dt;
            }
        }
        
        if (count($timestamps) < 2) {
            return 0.5; // Default to 30 minutes
        }
        
        sort($timestamps);
        $totalIntervals = 0;
        $intervalCount = 0;
        
        for ($i = 1; $i < count($timestamps); $i++) {
            $interval = $timestamps[$i] - $timestamps[$i-1];
            if ($interval > 0 && $interval < 7200) { // Ignore intervals > 2 hours (likely data gaps)
                $totalIntervals += $interval;
                $intervalCount++;
            }
        }
        
        if ($intervalCount === 0) {
            return 0.5; // Default to 30 minutes
        }
        
        $averageIntervalSeconds = $totalIntervals / $intervalCount;
        $averageIntervalHours = $averageIntervalSeconds / 3600;
        
        Log::info("Calculated time interval", [
            'interval_seconds' => $averageIntervalSeconds,
            'interval_hours' => $averageIntervalHours,
            'sample_size' => $intervalCount
        ]);
        
        return round($averageIntervalHours, 3);
    }
}
