<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Devices by Controller & Feed') }}
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('devices.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    ← Back to All Devices
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
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
                            <h3 class="text-xl font-bold text-gray-900">Devices by Controller & Feed</h3>
                            <p class="text-sm text-gray-500 mt-1">Total Controllers: {{ $controllerData->count() }}</p>
                        </div>
                    </div>

                    @if($controllerData->count() > 0)
                        <div class="space-y-6">
                            @foreach($controllerData as $controller)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <!-- Controller Header -->
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-lg font-semibold text-blue-800">
                                                    Controller #{{ $controller['controller_short_id'] }} (CONTROLLER)
                                                </h4>
                                                <p class="text-sm text-gray-600 mt-1">Serial No. {{ $controller['serial_no'] }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Feeds for this Controller -->
                                    @foreach($controller['feeds'] as $feed)
                                        <div class="border-t border-gray-200">
                                            <!-- Main Feed Header -->
                                            <div class="bg-gray-50 p-3 border-l-4 border-indigo-400">
                                                <h5 class="text-md font-semibold text-indigo-800">
                                                    Main Feed ID: {{ $feed['feed_uid'] }}
                                                </h5>
                                            </div>

                                            <!-- Devices Table -->
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8"></th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manufacturer</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent ?</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @foreach($feed['parent_devices'] as $parentDevice)
                                                            <!-- Parent Device Row -->
                                                            <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $parentDevice['has_slaves'] ? 'cursor-pointer' : '' }}" 
                                                                @if($parentDevice['has_slaves']) onclick="toggleSlaves('parent-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}')" @endif>
                                                                <td class="px-3 py-4 whitespace-nowrap text-center">
                                                                    @if($parentDevice['has_slaves'])
                                                                        <span id="icon-parent-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}" 
                                                                              class="text-blue-600 font-bold text-lg cursor-pointer">+</span>
                                                                    @else
                                                                        <span class="text-gray-300">●</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <a href="{{ route('devices.show', $parentDevice['id']) }}" 
                                                                       class="text-blue-600 hover:text-blue-800 font-medium"
                                                                       onclick="event.stopPropagation();">
                                                                        {{ $parentDevice['short_id'] ?? 'N/A' }}
                                                                    </a>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $parentDevice['type'] }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $parentDevice['manufacturer'] }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $parentDevice['model'] }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                                        {{ $parentDevice['status'] === 'Working' || $parentDevice['status'] === 'Ready' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                                        {{ $parentDevice['status'] }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $parentDevice['assigned_to'] }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span class="text-sm {{ $parentDevice['is_parent'] === 'Yes' ? 'text-green-600 font-medium' : 'text-gray-500' }}">
                                                                        {{ $parentDevice['is_parent'] }}
                                                                    </span>
                                                                </td>
                                                            </tr>

                                                            <!-- Slave Devices (Initially Hidden) -->
                                                            @if($parentDevice['has_slaves'])
                                                                @foreach($parentDevice['slaves'] as $slaveDevice)
                                                                    <tr class="slave-row parent-{{ $loop->parent->parent->parent->index }}-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }} hidden bg-gray-50 border-l-4 border-blue-200">
                                                                        <td class="px-3 py-4 whitespace-nowrap text-center">
                                                                            <span class="text-gray-400 text-sm">→</span>
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap pl-8">
                                                                            <a href="{{ route('devices.show', $slaveDevice['id']) }}" 
                                                                               class="text-blue-600 hover:text-blue-800 font-medium">
                                                                                {{ $slaveDevice['short_id'] ?? 'N/A' }}
                                                                            </a>
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                                            {{ $slaveDevice['type'] }}
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                                            {{ $slaveDevice['manufacturer'] }}
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                                            {{ $slaveDevice['model'] }}
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                                                {{ $slaveDevice['status'] === 'Working' || $slaveDevice['status'] === 'Ready' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                                                {{ $slaveDevice['status'] }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                                            {{ $slaveDevice['assigned_to'] }}
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                                            <span class="text-sm text-gray-500">
                                                                                {{ $slaveDevice['is_parent'] }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-lg mb-4">📡</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Controllers Found</h3>
                            <p class="text-gray-500">No controllers with devices were found in the system.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for collapsible functionality -->
    <script>
        function toggleSlaves(parentId) {
            const slaveRows = document.querySelectorAll(`.${parentId}`);
            const icon = document.getElementById('icon-' + parentId);
            
            if (slaveRows.length > 0) {
                const isHidden = slaveRows[0].classList.contains('hidden');
                
                slaveRows.forEach(row => {
                    if (isHidden) {
                        row.classList.remove('hidden');
                        row.classList.add('animate-slideDown');
                    } else {
                        row.classList.add('hidden');
                        row.classList.remove('animate-slideDown');
                    }
                });
                
                if (icon) {
                    icon.textContent = isHidden ? '−' : '+';
                }
            }
        }

        // Auto-dismiss success alert
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.add('opacity-0');
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            }
        });
    </script>

    <!-- Custom CSS for styling -->
    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                max-height: 200px;
                transform: translateY(0);
            }
        }

        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }

        /* Status badge styles */
        .bg-green-100 { background-color: #dcfce7; }
        .text-green-800 { color: #166534; }
        .bg-yellow-100 { background-color: #fef3c7; }
        .text-yellow-800 { color: #92400e; }

        /* Custom scrollbar for overflow areas */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Parent device hover effect */
        .cursor-pointer:hover {
            background-color: #f8fafc !important;
        }

        /* Slave device styling */
        .slave-row {
            border-left: 4px solid #bfdbfe;
        }
    </style>
</x-app-layout>
