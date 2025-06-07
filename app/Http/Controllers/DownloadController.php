<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DownloadController extends Controller
{
    /**
     * Download a chart in the specified format
     *
     * @param Plant $plant The plant model (bound by uid)
     * @param string $chart The chart type (energy, battery, savings)
     * @param string $type The download type (png, csv, pdf)
     * @return \Illuminate\Http\Response
     */
    public function download(Plant $plant, string $chart, string $type)
    {
        // Validate chart type
        if (!in_array($chart, ['energy', 'battery', 'savings'])) {
            return back()->with('error', 'Invalid chart type.');
        }

        // Validate download type
        if (!in_array($type, ['png', 'csv', 'pdf'])) {
            return back()->with('error', 'Invalid download type.');
        }

        // Dispatch to appropriate download method
        try {
            switch ($type) {
                case 'png': return $this->downloadPNG($plant, $chart);
                case 'csv': return $this->downloadCSV($plant, $chart);
                case 'pdf': return $this->downloadPDF($plant, $chart);
                default: abort(404);
            }
        } catch (\Exception $e) {
            Log::error("Chart download failed: " . $e->getMessage(), [
                'plant_uid' => $plant->uid,
                'chart' => $chart,
                'type' => $type,
                'exception' => $e
            ]);
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Download chart as PNG image
     */
    protected function downloadPNG(Plant $plant, string $chart)
    {
        // Construct file path using plant uid for consistency
        $file = public_path("charts/{$plant->uid}_{$chart}.png");

        // Check if file exists
        if (!file_exists($file)) {
            return back()->with('error', 'Chart image not found. Please generate the chart first.');
        }

        // Generate a nice filename for the download
        $filename = "{$plant->name}_{$chart}_" . date('Y-m-d') . ".png";

        // Return file download response
        return response()->download($file, $filename);
    }

    /**
     * Download chart data as CSV
     */
    protected function downloadCSV(Plant $plant, string $chart)
    {
        // Get appropriate data file based on chart type
        $file = match ($chart) {
            'energy', 'battery' => public_path("energy_live_chart.json"),
            'savings' => public_path("battery_savings.json"),
            default => null,
        };

        // Check if data file exists
        if (!$file || !file_exists($file)) {
            return back()->with('error', 'Chart data not found.');
        }

        // Load JSON data
        $data = json_decode(file_get_contents($file), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Invalid chart data format.');
        }

        // Generate filename for CSV
        $csvName = "{$plant->name}_{$chart}_" . date('Y-m-d') . ".csv";

        // Set CSV response headers
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$csvName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Create CSV streaming response
        $callback = function () use ($data, $chart) {
            // Open output stream
            $out = fopen('php://output', 'w');

            // Add CSV headers based on chart type
            match ($chart) {
                'energy' => fputcsv($out, ['Time', 'PV (kW)', 'Battery (kW)', 'Grid (kW)']),
                'battery' => fputcsv($out, ['Time', 'Battery Power (kW)', 'Energy Price (€/kWh)']),
                'savings' => fputcsv($out, ['Time', 'Battery Savings (€)']),
            };

            // Add data rows with formatted values
            foreach ($data as $ts => $val) {
                match ($chart) {
                    'energy' => fputcsv($out, [
                        $ts,
                        number_format($val['pv_p'] / 1000, 2),
                        number_format($val['battery_p'] / 1000, 2),
                        number_format($val['grid_p'] / 1000, 2)
                    ]),
                    'battery' => fputcsv($out, [
                        $ts,
                        number_format($val['battery_p'] / 1000, 2),
                        number_format($val['tariff'], 4)
                    ]),
                    'savings' => fputcsv($out, [
                        $ts,
                        number_format($val['battery_savings'], 2)
                    ]),
                };
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download chart as PDF document
     */
    protected function downloadPDF(Plant $plant, string $chart)
    {
        // Get appropriate data file based on chart type
        $dataFile = match ($chart) {
            'energy', 'battery' => public_path("energy_live_chart.json"),
            'savings' => public_path("battery_savings.json"),
            default => null,
        };

        // Path to the chart image
        $imagePath = public_path("charts/{$plant->uid}_{$chart}.png");

        // Check if required files exist
        if (!$dataFile || !file_exists($dataFile)) {
            return back()->with('error', 'Chart data not found.');
        }

        if (!file_exists($imagePath)) {
            return back()->with('error', 'Chart image not found. Please generate the chart first.');
        }

        // Load chart data
        $data = json_decode(file_get_contents($dataFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Invalid chart data format.');
        }

        // Convert chart PNG to base64 for embedding in PDF
        $imageData = base64_encode(file_get_contents($imagePath));
        $image = 'data:image/png;base64,' . $imageData;

        // Calculate summary data based on chart type
        $summary = $this->calculateSummary($data, $chart);

        // Generate PDF using Laravel-DomPDF
        $pdf = PDF::loadView("plants.exports.pdf", [
            'plant' => $plant,
            'chart' => $chart,
            'image' => $image,
            'data' => $data,
            'summary' => $summary,
            'generatedDate' => now()->format('Y-m-d H:i:s')
        ]);

        // Set PDF options (optional)
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        // Generate filename for PDF
        $filename = "{$plant->name}_{$chart}_" . date('Y-m-d') . ".pdf";

        // Return PDF download response
        return $pdf->download($filename);
    }

    /**
     * Calculate summary data for PDF exports
     */
    protected function calculateSummary($data, string $chart): array
    {
        $summary = [];

        switch ($chart) {
            case 'energy':
                $summary['totalPV'] = array_sum(array_map(fn($v) => $v['pv_p'] / 1000, $data));
                $summary['totalBattery'] = array_sum(array_map(fn($v) => $v['battery_p'] / 1000, $data));
                $summary['totalGrid'] = array_sum(array_map(fn($v) => $v['grid_p'] / 1000, $data));
                break;

            case 'battery':
                $totalPositive = array_sum(array_map(fn($v) => $v['battery_p'] > 0 ? $v['battery_p'] / 1000 : 0, $data));
                $totalNegative = array_sum(array_map(fn($v) => $v['battery_p'] < 0 ? $v['battery_p'] / 1000 : 0, $data));
                $summary['batteryCharged'] = $totalPositive;
                $summary['batteryDischarged'] = abs($totalNegative);
                $summary['avgTariff'] = array_sum(array_map(fn($v) => $v['tariff'], $data)) / count($data);
                break;

            case 'savings':
                $summary['totalSavings'] = array_sum(array_map(fn($v) => $v['battery_savings'], $data));
                break;
        }

        return $summary;
    }

    /**
     * Save chart image for later download
     */
    public function saveChartImage(Request $request, Plant $plant)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'chart' => 'required|string|in:energy,battery,savings',
                'image' => 'required|string'
            ]);

            $chart = $validated['chart'];
            $imageData = $validated['image'];

            // Process base64 image data
            if (!preg_match('/^data:image\/png;base64,/', $imageData)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid image format. Must be a base64-encoded PNG image.'
                ], 400);
            }

            // Extract the base64 encoded image data
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $decodedImage = base64_decode($imageData);

            if ($decodedImage === false) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid base64 image data'
                ], 400);
            }

            // Ensure the charts directory exists
            $dir = public_path("charts");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Check if directory is writable
            if (!is_writable($dir)) {
                Log::error("Charts directory is not writable: {$dir}");
                return response()->json([
                    'success' => false,
                    'error' => 'Server configuration error: charts directory is not writable'
                ], 500);
            }

            // Save the image file using the plant uid
            $path = "{$dir}/{$plant->uid}_{$chart}.png";
            if (file_put_contents($path, $decodedImage) === false) {
                Log::error("Failed to save chart image: {$path}");
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to save image'
                ], 500);
            }

            Log::info("Chart image saved successfully", [
                'plant_uid' => $plant->uid,
                'chart' => $chart,
                'path' => $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chart image saved successfully',
                'path' => $path
            ]);
        } catch (\Exception $e) {
            Log::error("Chart image save failed: " . $e->getMessage(), [
                'plant_uid' => $plant->uid ?? 'unknown',
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Server error while processing image: ' . $e->getMessage()
            ], 500);
        }
    }

}
