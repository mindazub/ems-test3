{{-- resources/views/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ $plant->name }}
        </h2>
    </x-slot>

    {{-- External Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    {{-- Page Content --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- PLANT INFO SECTION -->
                <div class="mb-6 flex flex-wrap gap-6">
                    {{-- General Info --}}
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h2 class="text-lg font-semibold mb-2">General Info</h2>
                        <p><strong>Owner Email:</strong> {{ $plant->owner_email }}</p>
                        <p><strong>Status:</strong> {{ $plant->status }}</p>
                        <p><strong>Capacity:</strong> {{ $plant->capacity }} W</p>
                        <p><strong>Location:</strong> Lat {{ $plant->latitude }}, Lng {{ $plant->longitude }}</p>
                        <p><strong>Last Updated:</strong>
                            {{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d H:i') : 'N/A' }}
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('plants.index') }}" class="btn btn-sm btn-primary">Back</a>
                            {{-- Add routes for Edit/Delete --}}
                            {{-- <a href="{{ route('plants.edit', $plant) }}" class="btn btn-sm btn-warning">Edit</a> --}}
                            {{-- <form action="{{ route('plants.destroy', $plant) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');"> --}}
                            {{-- @csrf --}}
                            {{-- @method('DELETE') --}}
                            {{-- <button type="submit" class="btn btn-sm btn-danger">Delete</button> --}}
                            {{-- </form> --}}
                        </div>
                    </div>
                    {{-- Map --}}
                    <div class="w-full lg:w-1/3">
                        <h3 class="text-lg font-semibold mb-2">Map Location</h3>
                        <div id="map" class="rounded shadow border" style="height: 200px;"></div>
                    </div>
                </div>

                <!-- STACKED CHARTS -->
                @include('plants.partials.plant-chart')

                <!-- DEVICES LIST -->
                @include('plants.partials.devices-list')

            </div> {{-- End bg-white --}}

        </div> {{-- End max-w-7xl --}}
    </div> {{-- End py-6 --}}

    {{-- JavaScript Libraries (Load AFTER HTML content) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    {{-- Custom Page Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- Leaflet Map Initialization ---
            const mapContainer = document.getElementById('map');
            if (mapContainer && typeof L !== 'undefined') { // Check if Leaflet loaded
                try {
                    const lat = {{ $plant->latitude ?? 0 }}; // Default coordinates if null
                    const lng = {{ $plant->longitude ?? 0 }};
                    const zoomLevel = ({{ $plant->latitude ? 1 : 0 }} === 0) ? 2 : 13; // Lower zoom if no coords

                    const map = L.map('map').setView([lat, lng], zoomLevel);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    if ({{ $plant->latitude ? 1 : 0 }}) { // Only add marker if coords exist
                        L.marker([lat, lng]).addTo(map)
                            .bindPopup(`Plant: {{ addslashes($plant->name) }}`) // Use addslashes for safety
                            .openPopup();
                    }
                } catch (e) {
                    console.error("Error initializing Leaflet map:", e);
                    mapContainer.innerHTML = '<p class="text-danger text-center">Error loading map.</p>';
                }
            } else if (mapContainer) {
                mapContainer.innerHTML =
                '<p class="text-warning text-center">Map library (Leaflet) not loaded.</p>';
            }

            // --- Bootstrap Tooltip Initialization ---
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip === 'function') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            } else {
                console.warn("Bootstrap Tooltip component not available.");
            }


            // --- Bootstrap Collapse Icon Toggle Logic ---
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Collapse === 'function') {
                // Find all the collapsible elements (the rows that hide/show)
                const collapseElements = document.querySelectorAll('.collapse.slave-row');

                collapseElements.forEach(collapseEl => {
                    // Find the button that controls this specific collapsible element ONCE
                    const triggerElement = document.querySelector(`[data-bs-target="#${collapseEl.id}"]`);
                    const icon = triggerElement ? triggerElement.querySelector('.toggle-icon') : null;

                    if (!icon) return; // Skip if trigger or icon not found

                    // Event listener for when the row STARTS to be shown
                    collapseEl.addEventListener('show.bs.collapse', event => {
                        icon.classList.remove('bi-plus-circle-fill');
                        icon.classList.add('bi-dash-circle-fill');
                    });

                    // Event listener for when the row STARTS to be hidden
                    collapseEl.addEventListener('hide.bs.collapse', event => {
                        icon.classList.remove('bi-dash-circle-fill');
                        icon.classList.add('bi-plus-circle-fill');
                    });

                    // Optional: Set initial icon state correctly if element starts shown (rare for collapse)
                    if (collapseEl.classList.contains('show')) {
                        icon.classList.remove('bi-plus-circle-fill');
                        icon.classList.add('bi-dash-circle-fill');
                    }

                });
            } else {
                console.warn("Bootstrap Collapse component not available.");
            }
            // --- End of Collapse Icon Toggle Logic ---

        }); // End of DOMContentLoaded
    </script>

</x-app-layout>
