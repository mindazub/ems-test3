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
        return view('plants.create', [
            'companies' => \App\Models\Company::all(), // optional if companies exist
        ]);
    }


    public function store(Request $request)
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

        Plant::create([
            'name' => $request->name,
            'owner_email' => $request->owner_email,
            'status' => $request->status,
            'capacity' => $request->capacity,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'company_id' => $request->company_id,
            'last_updated' => $request->last_updated ? \Carbon\Carbon::parse($request->last_updated)->timestamp : null,
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
