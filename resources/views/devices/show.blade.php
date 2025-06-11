<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold mb-4">Device Details</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <table class="table-auto w-full mb-6">
            <tbody>
                <tr>
                    <th class="text-left pr-4">ID</th>
                    <td>{{ $device['short_id'] ?? (isset($device['full_uid']) ? substr($device['full_uid'], 0, 8) : ($device['id'] ?? '-')) }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Full UID</th>
                    <td>{{ $device['full_uid'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Type</th>
                    <td>{{ $device['device_type'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Manufacturer</th>
                    <td>{{ $device['manufacturer'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Model</th>
                    <td>{{ $device['device_model'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Status</th>
                    <td>{{ $device['device_status'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Plant UID</th>
                    <td>{{ $device['plant_uid'] }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Plant Name</th>
                    <td>{{ $device['plant_name'] }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Controller UID</th>
                    <td>{{ $device['controller_uid'] }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Feed UID</th>
                    <td>{{ $device['feed_uid'] }}</td>
                </tr>
                <tr>
                    <th class="text-left pr-4">Parent Device ID</th>
                    <td>{{ $device['parent_device_short_id'] ?? ($device['parent_device_id'] ? substr($device['parent_device_id'], 0, 8) : '-') }}</td>
                </tr>
            </tbody>
        </table>
        <h3 class="text-lg font-semibold mb-2">Raw Device Data</h3>
        <pre class="bg-gray-100 p-4 rounded text-xs overflow-x-auto">{{ json_encode($device['raw'], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
        <div class="mt-6 flex gap-4">
            <a href="{{ url('/devices') }}" class="text-blue-600 hover:underline">&larr; Back to Device List</a>
            @if($device['plant_uid'] && ($device['plant_full_uid'] ?? false))
                <a href="{{ url('/plants/' . $device['plant_full_uid']) }}" class="text-green-700 hover:underline">&larr; Back to Plant</a>
            @endif
        </div>
            </div>
        </div>
    </div>
</x-app-layout>
