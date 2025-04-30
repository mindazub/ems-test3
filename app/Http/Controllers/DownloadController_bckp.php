<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadController extends Controller
{
    public function download(Plant $plant, string $chart, string $type)
    {
        switch ($type) {
            case 'png': return $this->downloadPNG($plant, $chart);
            case 'csv': return $this->downloadCSV($plant, $chart);
            case 'pdf': return $this->downloadPDF($plant, $chart);
            default: abort(404);
        }
    }

    protected function downloadPNG(Plant $plant, string $chart)
    {
        $file = public_path("charts/{$plant->id}_{$chart}.png");

        if (!file_exists($file)) {
            return back()->with('error', 'Chart PNG not found.');
        }

        return response()->download($file, "{$plant->name}_{$chart}.png");
    }

    protected function downloadCSV(Plant $plant, string $chart)
    {
        $file = match ($chart) {
            'energy', 'battery' => public_path("energy_live_chart.json"),
            'savings' => public_path("battery_savings.json"),
            default => null,
        };

        if (!$file || !file_exists($file)) {
            return back()->with('error', 'Data file not found.');
        }

        $data = json_decode(file_get_contents($file), true);

        $csvName = "{$plant->name}_{$chart}.csv";
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$csvName}"
        ];

        $callback = function () use ($data, $chart) {
            $out = fopen('php://output', 'w');

            match ($chart) {
                'energy' => fputcsv($out, ['Time', 'PV', 'Battery', 'Grid']),
                'battery' => fputcsv($out, ['Time', 'Battery Power', 'Tariff']),
                'savings' => fputcsv($out, ['Time', 'Battery Savings']),
            };

            foreach ($data as $ts => $val) {
                match ($chart) {
                    'energy' => fputcsv($out, [$ts, $val['pv_p'], $val['battery_p'], $val['grid_p']]),
                    'battery' => fputcsv($out, [$ts, $val['battery_p'], $val['tariff']]),
                    'savings' => fputcsv($out, [$ts, $val['battery_savings']]),
                };
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function downloadPDF(Plant $plant, string $chart)
    {
        $dataFile = match ($chart) {
            'energy', 'battery' => public_path("energy_live_chart.json"),
            'savings' => public_path("battery_savings.json"),
            default => null,
        };


        $imagePath = public_path("charts/{$plant->id}_{$chart}.png");

        if (!file_exists($imagePath)) {
            return back()->with('error', 'Chart image not found.');
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $image = 'data:image/png;base64,' . $imageData;


        if (!$dataFile || !file_exists($dataFile)) {
            return back()->with('error', 'Chart data not found.');
        }

        $data = json_decode(file_get_contents($dataFile), true);

        return PDF::loadView("plants.exports.pdf", [
            'plant' => $plant,
            'chart' => $chart,
            'image' => $image,
            'data' => $data
        ])->download("{$plant->name}_{$chart}.pdf");
    }
}
