<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ $plant->name }}
        </h2>
    </x-slot>

    {{-- Bootstrap for Collapse + Tooltip --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <!-- Map and General Info Side-by-side -->
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
                    <div class="w-full lg:w-1/2">
                        <h3 class="text-lg font-semibold mb-2">Map Location</h3>
                        <div id="map" class="rounded shadow border" style="height: 200px; min-height: 200px;">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Devices by Feed</h3>

                    @foreach ($plant->mainFeeds as $feed)
                        <div class="mb-6 border rounded p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-blue-600">Main Feed ID: {{ $feed->id }}</h4>
                                <button onclick="window.print()"
                                    class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                    <i class="bi bi-printer"></i> Export PDF
                                </button>
                            </div>
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
                                    @foreach ($feed->devices->where('parent_device', true) as $parent)
                                        <tr class="bg-gray-100">
                                            <td class="border px-4 py-2">{{ $parent->device_type }}</td>
                                            <td class="border px-4 py-2">{{ $parent->manufacturer }}</td>
                                            <td class="border px-4 py-2">
                                                <button class="btn btn-sm btn-link p-0" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#parent-{{ $parent->id }}">
                                                    <i class="bi bi-caret-down-fill"></i> {{ $parent->device_model }}
                                                </button>
                                            </td>
                                            <td class="border px-4 py-2">{{ $parent->device_status }}</td>
                                            <td class="border px-4 py-2">Yes</td>
                                            <td class="border px-4 py-2">
                                                <i class="bi bi-eye-fill text-blue-500" tabindex="0"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ json_encode($parent->parameters, JSON_PRETTY_PRINT) }}"></i>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="parent-{{ $parent->id }}">
                                            <td colspan="6" class="p-0">
                                                <table class="w-full">
                                                    <tbody>
                                                        @foreach ($feed->devices->where('parent_device', false) as $child)
                                                            @if ($child->main_feed_id === $parent->main_feed_id)
                                                                <tr>
                                                                    <td class="border px-4 py-2">
                                                                        {{ $child->device_type }}</td>
                                                                    <td class="border px-4 py-2">
                                                                        {{ $child->manufacturer }}</td>
                                                                    <td class="border px-4 py-2">
                                                                        {{ $child->device_model }}</td>
                                                                    <td class="border px-4 py-2">
                                                                        {{ $child->device_status }}</td>
                                                                    <td class="border px-4 py-2">No</td>
                                                                    <td class="border px-4 py-2">
                                                                        <i class="bi bi-eye-fill text-blue-500"
                                                                            tabindex="0" data-bs-toggle="tooltip"
                                                                            data-bs-placement="top"
                                                                            title="{{ json_encode($child->parameters, JSON_PRETTY_PRINT) }}"></i>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach

                    <div class="mt-6">
                        <a href="{{ route('plants.index') }}"
                            class="inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to
                            Plants</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Leaflet Map JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
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
            }

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</x-app-layout>
