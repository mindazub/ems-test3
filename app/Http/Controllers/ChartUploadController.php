<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ChartUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'plant_id' => 'required|integer',
            'chart' => 'required|string',
            'image' => 'required|string',
        ]);

        $plantId = $request->plant_id;
        $chart = strtolower($request->chart); // 'energy', 'battery', 'savings'

        // Clean base64 image string
        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->image);
        $imageData = str_replace(' ', '+', $imageData);

        // Path: public/charts/3_energy.png
        $filename = "{$plantId}_{$chart}.png";
        $path = public_path("charts/{$filename}");

        // Ensure directory exists
        File::ensureDirectoryExists(public_path('charts'));

        // Save decoded image
        File::put($path, base64_decode($imageData));

        return response()->json(['message' => 'Chart image uploaded', 'path' => "/charts/{$filename}"]);
    }
}
