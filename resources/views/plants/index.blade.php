<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('My Plants') }}
            </h2>
        </div>
    </x-slot>

    {{-- DataTables plain CSS --}}
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

    <div id="page-content" class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">

                    @if (session('message'))
                        <div id="success-alert" class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded relative flex items-center justify-between" role="alert">
                            <span>{{ session('message') }}</span>
                            <button type="button" class="text-green-800 hover:text-green-900 focus:outline-none ml-2" onclick="this.closest('div').remove();">
                                <span class="sr-only">Close</span>
                                &times;
                            </button>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-3">
                        <h3 class="mb-0 text-lg font-bold">Plants Table</h3>
                        <a href="{{ route('plants.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow transition">
                            {{-- Plus icon (Heroicons via Blade, if available) --}}
                            <x-heroicon-o-plus-circle class="w-5 h-5 mr-1" />
                            New Plant
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="plantsTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plant ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Main Feeds</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Devices</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Last Updated</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($plants as $plant)
                                <tr class="clickable-row transition hover:bg-indigo-100 cursor-pointer" data-href="{{ route('plants.show', $plant) }}">
                                    <td class="px-4 py-2"> {{ $plant->id }}</td>
                                    <td class="px-4 py-2">{{ $plant->name }}</td>
                                    <td class="px-4 py-2">{{ $plant->owner_email }}</td>
                                    <td class="px-4 py-2">{{ $plant->mainFeeds->count() }}</td>
                                    <td class="px-4 py-2">{{ $plant->mainFeeds->flatMap->devices->count() }}</td>
                                    <td class="px-4 py-2">{{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <a href="{{ route('plants.edit', $plant) }}" class="inline-block px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition">Edit</a>
                                        <form method="POST" action="{{ route('plants.destroy', $plant) }}" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-gray-400 py-4">No plants found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTables JS --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#plantsTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });

            $('#plantsTable').on('click', '.clickable-row', function(e) {
                if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('form').length) {
                    return;
                }
                window.location = $(this).data("href");
            });
        });
    </script>

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

    <style>
        .dataTables_length select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background: white !important;
            background-image: none !important;
            border: 1px solid #d1d5db;
            color: #111827;
            border-radius: 0.375rem;
            padding: 0.25rem 0.75rem 0.25rem 0.5rem;
            font-size: 0.875rem;
            box-shadow: none;
        }
    </style>
</x-app-layout>
