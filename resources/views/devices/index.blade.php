<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Devices') }}
            </h2>
            <a href="{{ route('devices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow transition">
                <!-- Heroicon Plus Circle -->
                <x-heroicon-o-plus-circle class="w-5 h-5 mr-1" />
                New Device
            </a>
        </div>
    </x-slot>

    {{-- ✅ DataTables CSS (plain, no Bootstrap) --}}
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

    <div id="page-content" class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">

                    {{-- ✅ Success Message --}}
                    @if (session('message'))
                        <div id="success-alert" class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded relative flex items-center justify-between" role="alert">
                            <span>{{ session('message') }}</span>
                            <button type="button" class="text-green-800 hover:text-green-900 focus:outline-none ml-2" onclick="this.closest('div').remove();">
                                <span class="sr-only">Close</span>
                                &times;
                            </button>
                        </div>
                    @endif

                    {{-- ✅ Table --}}
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="mb-0 text-lg font-bold">Devices Table</h3>
                        <a href="{{ route('devices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow transition">
                            {{-- Plus icon (Heroicons via Blade, if available) --}}
                            <x-heroicon-o-plus-circle class="w-5 h-5 mr-1" />
                            New Device
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="devicesTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Manufacturer</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plant</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Parent Device</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($devices as $device)
                            <tr class="clickable-row hover:bg-blue-50 cursor-pointer transition" data-device-id="{{ $device['id'] }}" data-href="{{ route('devices.show', $device['id']) }}">
                                <td class="px-4 py-2">
                                    <a href="{{ route('devices.show', $device['id']) }}" class="text-blue-600 hover:underline" title="{{ $device['id'] }}">
                                        {{ $device['short_id'] ?? (isset($device['id']) ? substr($device['id'], 0, 8) : '-') }}
                                    </a>
                                </td>
                                <td class="px-4 py-2">{{ $device['device_type'] }}</td>
                                <td class="px-4 py-2">{{ $device['manufacturer'] }}</td>
                                <td class="px-4 py-2">{{ $device['device_model'] }}</td>
                                <td class="px-4 py-2">{{ $device['device_status'] }}</td>
                                <td class="px-4 py-2">
                                    @if($device['plant_uid'])
                                        <a href="{{ url('/plants/' . ($device['plant_full_uid'] ?? $device['plant_uid'])) }}" class="text-blue-600 hover:text-blue-900 underline" title="{{ $device['plant_full_uid'] ?? $device['plant_uid'] }}">
                                            {{ $device['plant_short_uid'] ?? (isset($device['plant_uid']) ? substr($device['plant_uid'], 0, 8) : $device['plant_uid']) }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $device['parent_device_short_id'] ?? ($device['parent_device_id'] ? substr($device['parent_device_id'], 0, 8) : '') }}</td>
                                <td class="px-4 py-2 flex space-x-2">
                                    {{-- Actions can be left as is or removed for API devices --}}
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ✅ DataTables (no Bootstrap) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#devicesTable').DataTable({
                paging: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                info: true,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    zeroRecords: "No matching devices found",
                },
                ordering: true
            });

            // Make rows clickable
            $('#devicesTable').on('click', '.clickable-row', function(e) {
                if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('form').length) {
                    return;
                }
                window.location = $(this).data("href");
            });
        });
    </script>

    {{-- ✅ Tailwind Success Alert Fade Out --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.add('opacity-0');
                    setTimeout(() => alert.remove(), 1000);
                }, 2300);
            }
        });
    </script>

    {{-- ✅ Custom styles for clickable row (Tailwind version) --}}
    <style>
        .clickable-row {
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }

        .dataTables_length select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: white;
            border: 1px solid #d1d5db;
            color: #111827;
            border-radius: 0.375rem;
            padding: 0.25rem 0.75rem 0.25rem 0.5rem;
            font-size: 0.875rem;
            background-image: none !important; /* No custom arrow */
            box-shadow: none;
        }
        .dataTables_length select:focus,
        .dataTables_length select:active {
            background-image: none;
        }

        {{-- Highlight row if highlight param is present --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const highlightId = urlParams.get('highlight');
                if (highlightId) {
                    const row = document.querySelector(`[data-device-id="${highlightId}"]`);
                    if (row) {
                        row.classList.add('highlight-device-row');
                        setTimeout(() => {
                            row.classList.add('highlight-fade');
                            setTimeout(() => row.classList.remove('highlight-device-row', 'highlight-fade'), 1000);
                        }, 3000);
                    }
                }
            });
        </script>
        <style>
            .highlight-device-row {
                outline: 3px solid red !important;
                outline-offset: -3px;
                transition: outline 0.3s cubic-bezier(.4,2,.6,1), background 0.3s;
                z-index: 10;
                background: #fff7f7;
            }
            .highlight-fade {
                outline: 0px solid transparent !important;
                background: inherit;
                transition: outline 1s cubic-bezier(.4,2,.6,1), background 1s;
            }
        </style>
    </style>

</x-app-layout>
