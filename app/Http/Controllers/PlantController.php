<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PlantController extends Controller
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
            $plants = collect($plantsArr)->map(function ($plant) {
                $plantObj = new \stdClass();
                $plantObj->id = $plant['uid'] ?? null;
                $plantObj->name = $plant['uid'] ?? '';
                // Fetch user by UUID and set email
                $user = \App\Models\User::where('uuid', $plant['owner'] ?? '')->first();
                $plantObj->owner_email = $user ? $user->email : ($plant['owner'] ?? '');
                $plantObj->status = $plant['status'] ?? '';
                $plantObj->last_updated = $plant['updated_at'] ?? null;
                $plantObj->device_amount = $plant['device_amount'] ?? null;
                $plantObj->controllers = collect();
                return $plantObj;
            });
        } catch (\Exception $e) {
            
            $plants = collect();
        }

        // dd($plants);

        return view('plants.index', compact('plants'));
    }

    public function create()
    {
        return view('plants.create', [
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'status' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'last_updated' => 'nullable|date',
        ]);

        Plant::create([
            'name' => $request->name,
            'owner_email' => $request->owner_email,
            'status' => $request->status,
            'capacity' => $request->capacity,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'last_updated' => $request->last_updated
                ? \Carbon\Carbon::parse($request->last_updated)->timestamp
                : null,
        ]);

        Cache::forget('plants.with.controllers.mainFeeds.devices');

        return redirect()->route('plants.index')->with('message', 'Plant created successfully.');
    }


    public function edit(Plant $plant)
    {
        return view('plants.edit', compact('plant'));
    }

    public function update(Request $request, Plant $plant)
    {
        $request->validate([
            'name' => 'required|string',
            'owner_email' => 'required|email',
            'status' => 'required|string',
            'capacity' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'last_updated' => 'nullable|date',
        ]);

        $plant->update([
            'name' => $request->name,
            'owner_email' => $request->owner_email,
            'status' => $request->status,
            'capacity' => $request->capacity,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'last_updated' => $request->last_updated
                ? \Carbon\Carbon::parse($request->last_updated)->timestamp
                : null,
        ]);
        Cache::forget('plants.with.controllers.mainFeeds.devices');
        return redirect()->route('plants.index')->with('message', 'Plant updated successfully.');
    }


    public function destroy(Plant $plant)
    {
        $plant->delete();
        Cache::forget('plants.with.controllers.mainFeeds.devices');
        return redirect()->route('plants.index')->with('message', 'Plant deleted successfully.');
    }

    public function show(Plant $plant)
    {
            $id = $plant->id;
            $client = new \GuzzleHttp\Client();
            $url = "http://127.0.0.1:5001/plant_view/{$id}";
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
                $plant = json_decode($body, true);
            } catch (\Exception $e) {
                $plant = null;
            }
            return view('plants.show', compact('plant'));
        
    }

    public function showRemote($id)
    {
        $client = new \GuzzleHttp\Client();
        $url = "http://127.0.0.1:5001/plant_view/{$id}";
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
            $plant = json_decode($body, true);
        } catch (\Exception $e) {
            $plant = null;
        }
        return view('plants.show', compact('plant'));
    }
}
