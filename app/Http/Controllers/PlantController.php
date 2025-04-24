<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function index()
    {
        $plants = Plant::with('mainFeeds.devices')->paginate();
        return view('plants.index', compact('plants'));
    }

    public function create()
    {
        return view('plants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'status' => 'required|string',
            'capacity' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Plant::create([
            'name' => $request->name,
            'owner_email' => $request->owner_email,
            'status' => $request->status,
            'capacity' => $request->capacity,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'last_updated' => now()->timestamp,
        ]);

        return redirect()->route('plants.index')->with('message', 'Plant created successfully.');
    }

    public function edit(Plant $plant)
    {
        return view('plants.edit', compact('plant'));
    }

    public function update(Request $request, Plant $plant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'status' => 'required|string',
            'capacity' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $plant->update([
            'name' => $request->name,
            'owner_email' => $request->owner_email,
            'status' => $request->status,
            'capacity' => $request->capacity,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'last_updated' => now()->timestamp,
        ]);

        return redirect()->route('plants.index')->with('message', 'Plant updated successfully.');
    }

    public function destroy(Plant $plant)
    {
        $plant->delete();
        return redirect()->route('plants.index')->with('message', 'Plant deleted.');
    }

    public function show(Plant $plant)
    {
        $plant->load('mainFeeds.devices'); // Eager load to prevent N+1 and ensure data is available
        return view('plants.show', compact('plant'));
    }

}
