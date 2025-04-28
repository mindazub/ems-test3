<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MainFeed;
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
        return view('devices.create', [
            'mainFeeds' => MainFeed::all(),
            'parentDevices' => Device::where('parent_device', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'main_feed_id' => 'required|exists:main_feeds,id',
            'parent_device_id' => 'nullable|exists:devices,id',
            'device_type' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'device_model' => 'required|string|max:255',
            'device_status' => 'required|string|max:255',
            'parent_device' => 'nullable|boolean',
            'parameters' => 'nullable|json',
        ]);

        // Convert parameters string to array if filled
        if (!empty($validated['parameters'])) {
            $validated['parameters'] = json_decode($validated['parameters'], true);
        }

        Device::create($validated);

        return redirect()->route('devices.index')->with('message', 'Device created successfully.');
    }



    public function edit(Device $device)
    {
        // All MainFeeds for the dropdown
        $mainFeeds = \App\Models\MainFeed::with('plant')->get();

        // All possible parent devices (excluding this device itself to prevent circular parenting)
        $parentDevices = \App\Models\Device::where('parent_device', true)
                            ->where('id', '!=', $device->id)
                            ->get();

        return view('devices.edit', compact('device', 'mainFeeds', 'parentDevices'));
    }


    public function update(Request $request, Device $device)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'main_feed_id' => 'required|exists:main_feeds,id',
            'parent_id' => 'nullable|exists:devices,id',
        ]);

        $device->update($validatedData);

        return redirect()->route('devices.index')->with('message', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')->with('message', 'Device deleted successfully.');
    }
}
