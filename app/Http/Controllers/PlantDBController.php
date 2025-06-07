<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PlantDBController extends Controller
{
    public function index()
    {
        $plants = Plant::all();
        // Attach owner info for each plant
        $plants->transform(function ($plant) {
            $owner = null;
            if (!empty($plant->owner)) {
                $owner = \App\Models\Customer::on('edis_system_data')->where('uid', $plant->owner)->first();
            }
            $plant->owner_name = $owner ? ($owner->username ?? 'NA') : 'NA';
            $plant->owner_email = $owner ? ($owner->email ?? 'NA') : 'NA';
            return $plant;
        });
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

    // public function show(Plant $plant)
    // {
    //     $plant->load(['controllers.mainFeeds.devices']); // Eager load controllers, mainFeeds, and devices
    //     $user = auth()->user();
    //     return view('plants.show', compact('plant', 'user'));
    // }

    public function show($uid)
    {
        $plant = \App\Models\Plant::where('uid', $uid)->firstOrFail();
        // Fetch controllers for this plant
        $controllers = \App\Models\PlantController::where('plant_id', $plant->uid)->get();
        // For each controller, fetch mainfeeds and their devices
        foreach ($controllers as $controller) {
            if ($controller->id) {
                $controller->mainfeeds = \App\Models\MainFeed::where('plant_controller_id', $controller->id)->get();
                foreach ($controller->mainfeeds as $mainfeed) {
                    $mainfeed->setRelation('devices', \App\Models\Device::where('main_feed_id', $mainfeed->id)->get());
                }
            } else {
                $controller->mainfeeds = collect();
            }
        }
        $user = auth()->user();
        // Always pass $plant to the view for chart download JS
        return view('plants.show', ['plant' => $plant, 'controllers' => $controllers, 'user' => $user]);
    }
}
