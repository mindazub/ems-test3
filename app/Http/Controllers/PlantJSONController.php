<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PlantJSONController extends Controller
{
    public function index()
    {
        $client = new \GuzzleHttp\Client();
        $url = 'http://127.0.0.1:5001/plant_list/6a36660d-daae-48dd-a4fe-000b191b13d8';
        $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 5,
            ]);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            $plantsArr = $data['plants'] ?? [];
            // Debug: log the first plant structure
            if (!empty($plantsArr)) {
                \Log::debug('First plant structure', ['plant' => $plantsArr[0]]);
            }
            // Transform to objects with nested collections for Blade compatibility
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
        } catch (\Exception $e) {
            dd($plants);
            $plants = collect();
        }
        return view('plants.index', ['plants' => $plants]);
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
