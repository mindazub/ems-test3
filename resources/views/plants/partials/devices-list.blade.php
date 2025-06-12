<div class="mb-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Devices by Controller & Feed</h3>
        @if (!$plant)
            <div class="text-red-500 italic mb-4">Error loading plant data. Please try again later.</div>
        @elseif ($plant->controllers && count($plant->controllers))
            @foreach ($plant->controllers as $controller)
                <div class="mb-6 border-2 border-indigo-200 rounded-lg p-4 bg-indigo-50">
                    <div class="mb-2 flex items-center justify-between">
                        <h4 class="text-xl font-bold text-indigo-800">Controller #{{ substr($controller->uid ?? $controller->id ?? 'N/A', 0, 8) }} <span class="text-xs text-gray-500">( CONTROLLER)</span></h4>
                        <span class="text-xs text-gray-500">Serial No: {{ $controller->serial_number ?? substr($controller->id ?? 'N/A', 0, 8) }}</span>
                    </div>
                    @if (!empty($controller->mainFeeds) && count($controller->mainFeeds))
                        @foreach ($controller->mainFeeds as $feed)
                            <div class="mb-4 border rounded p-3 bg-white">
                                <div class="flex justify-between items-center mb-2">
                                    <h5 class="font-semibold text-indigo-700 mb-0">
                                        Main Feed ID: {{ $feed->uid ?? 'N/A' }}
                                    </h5>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-fixed text-sm border rounded">
                                        <thead class="bg-gray-50 border-b">
                                        <tr>
                                            <th class="w-10 px-2 py-2"></th>
                                            <th class="px-2 py-2 text-left font-semibold">ID</th>
                                            <th class="px-2 py-2 text-left font-semibold">Type</th>
                                            <th class="px-2 py-2 text-left font-semibold">Manufacturer</th>
                                            <th class="px-2 py-2 text-left font-semibold">Model</th>
                                            <th class="px-2 py-2 text-left font-semibold">Status</th>
                                            <th class="px-2 py-2 text-left font-semibold">Assigned To</th>
                                            <th class="px-2 py-2 text-left font-semibold">Parent ?</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (!empty($feed->devices))
                                            @foreach ($feed->devices as $device)
                                                @php
                                                    $deviceId = $device->id ?? $device->uid ?? '';
                                                    $fullId = $device->uid ?? $device->id ?? '';
                                                    $shortId = substr($deviceId, 0, 8);
                                                    
                                                    // Use pre-processed flags from controller (same logic as devices-by-feed)
                                                    $isParent = $device->is_true_parent ?? false;
                                                    $hasSlaves = $device->has_slaves_processed ?? false;
                                                    
                                                    $parentRowId = 'parent-' . $loop->parent->parent->index . '-' . $loop->parent->index . '-' . $loop->index;
                                                @endphp
                                                
                                                <!-- Parent Device Row -->
                                                <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $isParent ? 'cursor-pointer' : '' }}" 
                                                    @if($isParent) onclick="toggleSlaves('{{ $parentRowId }}')" @endif>
                                                    <td class="px-2 py-2 text-center">
                                                        @if($isParent)
                                                            <button type="button" class="focus:outline-none flex items-center justify-center" aria-expanded="false" aria-label="Toggle children">
                                                                <svg id="icon-{{ $parentRowId }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-plus w-5 h-5 text-blue-600 dark:text-blue-400">
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <path d="M8 12h8"></path>
                                                                    <path d="M12 8v8"></path>
                                                                </svg>
                                                            </button>
                                                        @else
                                                            <button class="flex items-center justify-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" 
                                                                     width="24" height="24" 
                                                                     viewBox="0 0 24 24" 
                                                                     fill="none" 
                                                                     stroke="currentColor" 
                                                                     stroke-width="2" 
                                                                     stroke-linecap="round" 
                                                                     stroke-linejoin="round" 
                                                                     class="lucide w-5 h-5 text-gray-400 dark:text-gray-500">
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-2">
                                                        <a href="{{ url('/devices/' . $fullId) }}" 
                                                           class="text-blue-600 hover:text-blue-800 font-medium"
                                                           onclick="event.stopPropagation();"
                                                           title="{{ $fullId }}">
                                                            {{ $shortId }}
                                                        </a>
                                                    </td>
                                                    <td class="px-2 py-2">{{ $device->device_type ?? '-' }}</td>
                                                    <td class="px-2 py-2">{{ $device->device_manufacturer ?? '-' }}</td>
                                                    <td class="px-2 py-2">{{ $device->device_model ?? '-' }}</td>
                                                    <td class="px-2 py-2">
                                                        @if(($device->device_status ?? '') === 'Working' || ($device->device_status ?? '') === 'Ready')
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                                {{ $device->device_status }}
                                                            </span>
                                                        @else
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                {{ $device->device_status ?? 'Unknown' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-2">
                                                        @if(!empty($device->parent_device_id))
                                                            @php
                                                                $parentFullId = $device->parent_device_id;
                                                                $parentShortId = substr($parentFullId, 0, 8);
                                                            @endphp
                                                            <a href="{{ url('/devices/' . $parentFullId) }}" 
                                                               class="text-blue-600 hover:text-blue-800" 
                                                               title="{{ $parentFullId }}">{{ $parentShortId }}</a>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-2 text-center">
                                                        @if($isParent)
                                                            <span class="text-green-600 font-bold">Yes</span>
                                                        @else
                                                            <span class="text-gray-400">No</span>
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Slave Devices (Initially Hidden) -->
                                                @if($isParent)
                                                    @foreach($device->assigned_devices as $slaveDevice)
                                                        @php
                                                            $slaveId = $slaveDevice->id ?? $slaveDevice->uid ?? '';
                                                            $slaveFullId = $slaveDevice->uid ?? $slaveDevice->id ?? '';
                                                            $slaveShortId = substr($slaveId, 0, 8);
                                                        @endphp
                                                        <tr class="slave-row {{ $parentRowId }} hidden bg-gray-50 border-l-4 border-blue-200">
                                                            <td class="px-2 py-2 text-center">
                                                                <div class="flex items-center justify-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right w-4 h-4 text-gray-400 dark:text-gray-500">
                                                                        <path d="M5 12h14"></path>
                                                                        <path d="m12 5 7 7-7 7"></path>
                                                                    </svg>
                                                                </div>
                                                            </td>
                                                            <td class="px-2 py-2 pl-8">
                                                                <a href="{{ url('/devices/' . $slaveFullId) }}" 
                                                                   class="text-blue-600 hover:text-blue-800 font-medium"
                                                                   title="{{ $slaveFullId }}">
                                                                    {{ $slaveShortId }}
                                                                </a>
                                                            </td>
                                                            <td class="px-2 py-2 text-gray-700">{{ $slaveDevice->device_type ?? '-' }}</td>
                                                            <td class="px-2 py-2 text-gray-700">{{ $slaveDevice->device_manufacturer ?? '-' }}</td>
                                                            <td class="px-2 py-2 text-gray-700">{{ $slaveDevice->device_model ?? '-' }}</td>
                                                            <td class="px-2 py-2">
                                                                @if(($slaveDevice->device_status ?? '') === 'Working' || ($slaveDevice->device_status ?? '') === 'Ready')
                                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                                        {{ $slaveDevice->device_status }}
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                        {{ $slaveDevice->device_status ?? 'Unknown' }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-2 text-gray-700">
                                                                @if(!empty($slaveDevice->parent_device_id))
                                                                    @php
                                                                        $slaveParentFullId = $slaveDevice->parent_device_id;
                                                                        $slaveParentShortId = substr($slaveParentFullId, 0, 8);
                                                                    @endphp
                                                                    <a href="{{ url('/devices/' . $slaveParentFullId) }}" 
                                                                       class="text-blue-600 hover:text-blue-800" 
                                                                       title="{{ $slaveParentFullId }}">{{ $slaveParentShortId }}</a>
                                                                @else
                                                                    —
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-2 text-center">
                                                                <span class="text-gray-400">No</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @else
                                            <tr><td colspan="8" class="text-center text-gray-400 italic py-4">No devices found for this feed.</td></tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-gray-500 italic mb-4">No main feeds found for this controller.</div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-gray-500 italic mb-4">No controllers found for this plant.</div>
        @endif
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
                // Toggle between plus and minus icons
                if (isHidden) {
                    // Change to minus icon (circle-minus)
                    icon.innerHTML = '<circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path>';
                    icon.classList.remove('lucide-circle-plus');
                    icon.classList.add('lucide-circle-minus');
                } else {
                    // Change to plus icon (circle-plus)
                    icon.innerHTML = '<circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path><path d="M12 8v8"></path>';
                    icon.classList.remove('lucide-circle-minus');
                    icon.classList.add('lucide-circle-plus');
                }
            }
        }
    }
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

    /* Parent device hover effect */
    .cursor-pointer:hover {
        background-color: #f8fafc !important;
    }

    /* Slave device styling */
    .slave-row {
        border-left: 4px solid #bfdbfe;
    }
</style>


