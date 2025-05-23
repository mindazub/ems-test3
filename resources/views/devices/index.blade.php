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
        <div class="max-w-7xl mx-auto px-4">
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
                    <h3 class="mb-4 text-lg font-bold">Devices Table</h3>

                    <div class="overflow-x-auto">
                        <table id="devicesTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Manufacturer</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Main Feed</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Parent Device</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($devices as $device)
                                <tr class="clickable-row hover:bg-blue-50 cursor-pointer transition" data-href="{{ route('devices.show', $device) }}">
                                    <td class="px-4 py-2">{{ $device->id }}</td>
                                    <td class="px-4 py-2">{{ $device->device_type }}</td>
                                    <td class="px-4 py-2">{{ $device->manufacturer }}</td>
                                    <td class="px-4 py-2">{{ $device->device_model }}</td>
                                    <td class="px-4 py-2">{{ $device->device_status }}</td>
                                    <td class="px-4 py-2">{{ $device->mainFeed->id ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $device->parent?->id ?? '—' }}</td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <a href="{{ route('devices.edit', $device) }}" class="inline-block px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition">Edit</a>
                                        <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition">Delete</button>
                                        </form>
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
    </style>

</x-app-layout>
