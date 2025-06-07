<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PlantJSONController extends Controller
{
    public function index()
    {
        $jsonPath = public_path('plant_view_65f20fa1-047a-4379-8464-59f1d94be3c7_1748955255.json');
        $data = [];
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
        }
        $plantsArr = $data['plants'] ?? (isset($data[0]) ? $data : (isset($data['name']) ? [$data] : []));
        // Convert array to collection of objects for compatibility with blade
        $plants = collect($plantsArr)->map(function ($plant) {
            $plantObj = (object) $plant;
            $plantObj->controllers = collect($plant['controllers'] ?? [])->map(function ($controller) {
                $controllerObj = (object) $controller;
                $controllerObj->mainFeeds = collect($controller['mainfeeds'] ?? [])->map(function ($feed) {
                    $feedObj = (object) $feed;
                    $feedObj->devices = collect($feed['devices'] ?? [])->map(fn($d) => (object) $d);
                    return $feedObj;
                });
                return $controllerObj;
            });
            return $plantObj;
        });
        return view('plants.index', compact('plants'));
    }

    public function indexJson()
    {
        $jsonPath = public_path('plant_view_65f20fa1-047a-4379-8464-59f1d94be3c7_1748955255.json');
        $data = [];
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
        }
        // Parse plants array or fallback
        $plants = $data['plants'] ?? (isset($data[0]) ? $data : (isset($data['name']) ? [$data] : []));
        return view('plants.index-json', compact('plants'));
    }
}
