<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ $plant->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <div class="mb-6 flex flex-wrap gap-6">
                    <!-- General Info -->
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h3 class="text-lg font-semibold mb-2">General Info</h3>
                        <p><strong>Owner Email:</strong> {{ $plant->owner_email }}</p>
                        <p><strong>Status:</strong> {{ $plant->status }}</p>
                        <p><strong>Capacity:</strong> {{ $plant->capacity }} W</p>
                        <p><strong>Location:</strong> Lat {{ $plant->latitude }}, Lng {{ $plant->longitude }}</p>
                        <p><strong>Last Updated:</strong>
                            {{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d H:i') : 'N/A' }}
                        </p>
                    </div>

                    <!-- Map -->
                    <div class="w-full lg:w-1/3">
                        <h3 class="text-lg font-semibold mb-2">Map Location</h3>
                        <div id="map" class="rounded shadow border" style="height: 200px; min-height: 200px;">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Devices Summary</h3>
                    <p>Total Main Feeds: <strong>{{ $plant->mainFeeds->count() }}</strong></p>
                    <p>Total Devices: <strong>{{ $plant->mainFeeds->flatMap->devices->count() }}</strong></p>
                    <p class="mt-2">
                        @php
                            $types = $plant->mainFeeds->flatMap->devices->groupBy('device_type');
                        @endphp
                        @foreach ($types as $type => $group)
                            <span
                                class="inline-block bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">
                                <i class="bi bi-cpu me-1"></i>{{ $type }} ({{ $group->count() }})
                            </span>
                        @endforeach
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Devices by Feed</h3>

                    @forelse ($plant->mainFeeds as $feed)
                        <div class="mb-6 border rounded p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-blue-600">Main Feed ID: {{ $feed->id }}</h4>
                                <button onclick="window.print()"
                                    class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                    <i class="bi bi-printer"></i> Export PDF
                                </button>
                            </div>
                            <p><strong>Import Power:</strong> {{ $feed->import_power }} W</p>
                            <p><strong>Export Power:</strong> {{ $feed->export_power }} W</p>

                            <table class="table-auto w-full mt-4 border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border">Type</th>
                                        <th class="px-4 py-2 border">Manufacturer</th>
                                        <th class="px-4 py-2 border">Model</th>
                                        <th class="px-4 py-2 border">Status</th>
                                        <th class="px-4 py-2 border">Parent</th>
                                        <th class="px-4 py-2 border">Parameters</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($feed->devices as $device)
                                        <tr>
                                            <td class="border px-4 py-2">{{ $device->device_type }}</td>
                                            <td class="border px-4 py-2">{{ $device->manufacturer }}</td>
                                            <td class="border px-4 py-2">{{ $device->device_model }}</td>
                                            <td class="border px-4 py-2">
                                                @if ($device->device_status === 'Working')
                                                    <span class="text-green-600 font-semibold"><i
                                                            class="bi bi-check-circle-fill"></i>
                                                        {{ $device->device_status }}</span>
                                                @else
                                                    <span class="text-red-600 font-semibold"><i
                                                            class="bi bi-exclamation-circle-fill"></i>
                                                        {{ $device->device_status }}</span>
                                                @endif
                                            </td>
                                            <td class="border px-4 py-2">{{ $device->parent_device ? 'Yes' : 'No' }}
                                            </td>
                                            {{-- icon eye --}}

                                            <td class="border px-4 py-2">
                                                @if ($device->parameters)
                                                    <i class="bi bi-eye-fill text-blue-500"></i>
                                                @else
                                                    <i class="bi bi-eye-slash-fill text-gray-400"></i>
                                                @endif
                                                <pre class="text-xs">{{ json_encode($device->parameters, JSON_PRETTY_PRINT) }}</pre>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center border py-2">No devices in this feed.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <p>No main feeds found for this plant.</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    <a href="{{ route('plants.index') }}"
                        class="inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to
                        Plants</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ICONS JS CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    {{-- Leaflet Map JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lat = {{ $plant->latitude }};
            const lng = {{ $plant->longitude }};

            const map = L.map('map').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup(`Plant: {{ $plant->name }}`)
                .openPopup();
        });
    </script>
</x-app-layout>
