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

    <div id="page-content" class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Image Section -->
            <div class="hero-image-container relative w-full h-64 md:h-80 lg:h-96 mb-12 rounded-xl overflow-hidden">
                <img src="{{ asset('images/EMS-showoff.jpg') }}" 
                     alt="EMS Dashboard Showcase" 
                     class="w-full h-full object-cover object-center">
                
                <!-- Content overlay -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="hero-content text-center text-white max-w-4xl px-8">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">
                            Your Plant Portfolio
                        </h1>
                        <p class="text-xl md:text-2xl opacity-90 mb-6">
                            Manage and monitor all your energy plants from one centralized dashboard
                        </p>
                        <div class="flex flex-wrap justify-center items-center gap-4">
                            <div class="bg-white/25 backdrop-blur-sm rounded-lg px-6 py-3 border border-white/40">
                                <span class="text-lg font-medium">{{ $plants->count() }} Online Plants</span>
                            </div>
                            <div class="bg-white/25 backdrop-blur-sm rounded-lg px-6 py-3 border border-white/40">
                                <span class="text-lg font-medium">0 Offline Plants</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
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

                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Plants Overview</h3>
                            <p class="text-sm text-gray-500 mt-1">Total: {{ $plants->count() }} plants</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="live-data-badge inline-flex items-center text-sm font-bold px-4 py-2 rounded-full shadow-lg">
                                <span class="w-2 h-2 bg-white rounded-full mr-2 animate-ping"></span>
                                Live Data
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="plantsTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plant ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Device Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Last Updated</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($plants as $plant)
                                <tr class="clickable-row cursor-pointer hover:bg-indigo-100" data-href="{{ route('plants.show', $plant->id) }}">
                                    <td class="px-4 py-2">{{ $plant->id }}</td>
                                    <td class="px-4 py-2">{{ $plant->owner_email }}</td>
                                    <td class="px-4 py-2">{{ $plant->status }}</td>
                                    <td class="px-4 py-2">{{ $plant->device_amount ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">
                                        {{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2">
                                    <a href="{{ route('plants.show', $plant->id) }}" class="text-blue-600 hover:text-blue-900">Show</a>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-2 text-center">No plants found.</td>
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

            // Make table rows clickable
            $(document).on('click', '.clickable-row', function(e) {
                // Don't trigger row click if user clicked on a link or button
                if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('a').length || $(e.target).closest('button').length) {
                    return;
                }
                
                // Get the URL from data-href attribute and navigate
                const url = $(this).data('href');
                if (url) {
                    window.location.href = url;
                }
            });

            // Add hover effect for better UX
            $(document).on('mouseenter', '.clickable-row', function() {
                $(this).addClass('cursor-pointer');
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
        /* DataTables styling improvements */
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

        /* Hero image enhancements */
        .hero-image-container {
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .hero-image-container img {
            filter: blur(3px);
            transform: scale(1.05);
        }

        .hero-image-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(30, 41, 59, 0.6) 50%, rgba(51, 65, 85, 0.5) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* Table enhancements */
        .clickable-row {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .clickable-row:hover {
            background-color: #e0e7ff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .clickable-row:active {
            transform: translateY(0);
            background-color: #c7d2fe !important;
        }

        /* Ensure links in the actions column are still clickable */
        .clickable-row td a {
            position: relative;
            z-index: 10;
        }

        /* Responsive image adjustments */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }
            
            .hero-content p {
                font-size: 1rem;
                line-height: 1.5rem;
            }
        }

        /* Live Data Badge Enhanced Visibility */
        .live-data-badge {
            background-color: #ef4444 !important;
            color: white !important;
            animation: live-glow 2s ease-in-out infinite;
            border: 2px solid #dc2626;
        }

        @keyframes live-glow {
            0%, 100% {
                box-shadow: 0 0 5px rgba(239, 68, 68, 0.5);
                background-color: #ef4444;
            }
            50% {
                box-shadow: 0 0 20px rgba(239, 68, 68, 0.8);
                background-color: #dc2626;
            }
        }
    </style>
</x-app-layout>
