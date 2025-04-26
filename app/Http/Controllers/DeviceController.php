<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::with(['mainFeed', 'parent'])->get();

        // Logic to retrieve and display devices
        return view('devices.index', compact('devices'));
    }

    public function show(Device $device)
    {
        $device->load(['mainFeed', 'parent', 'assignedDevices']);

        return view('devices.show', compact('device'));
    }

    public function create()
    {
        $devices = Device::all();
        return view('devices.create', compact('devices'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'main_feed_id' => 'required|exists:main_feeds,id',
            'parent_id' => 'nullable|exists:devices,id',
        ]);

        Device::create($validatedData);

        return redirect()->route('devices.index')->with('success', 'Device created successfully.');
    }

    public function edit(Device $device)
    {
        $devices = Device::all();
        return view('devices.edit', compact('device', 'devices'));
    }

    public function update(Request $request, Device $device)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'main_feed_id' => 'required|exists:main_feeds,id',
            'parent_id' => 'nullable|exists:devices,id',
        ]);

        $device->update($validatedData);

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}
