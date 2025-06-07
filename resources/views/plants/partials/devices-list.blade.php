@if (!function_exists('__device_row_blade'))
    @php
    function __device_row_blade($device, $level = 0, $parentKey = '') {
        $isParent = $device->parent_device ?? false;
        $hasChildren = !empty($device->assigned_devices);
        $rowKey = $parentKey . ($device->id ?? $device->uuid ?? uniqid());
        echo '<tr x-data="{ open: false }" data-device-id="' . ($device->id ?? $device->uid ?? '') . '">';
        // Collapsible toggle for parent devices with children
        echo '<td class="w-10 text-center">';
        if ($isParent && $hasChildren) {
            echo '<button type="button" class="focus:outline-none" @click="open = !open" :aria-expanded="open" aria-label="Toggle children">';
            echo '<template x-if="!open"><svg class="w-5 h-5 text-indigo-600 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></template>';
            echo '<template x-if="open"><svg class="w-5 h-5 text-indigo-600 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg></template>';
            echo '</button>';
        } else {
            echo $level > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '↳' : '';
        }
        echo '</td>';
        // ID column with link to /devices?highlight={id}
        $deviceId = $device->id ?? $device->uid ?? '';
        $shortId = substr($deviceId, 0, 8);
        echo '<td class="px-2 py-2 cursor-pointer">';
        echo '<a href="' . url('/devices?highlight=' . $shortId) . '" class="text-blue-600 hover:underline">' . $shortId . '</a>';
        echo '</td>';
        // Device info columns
        echo '<td class="px-2 py-2">' . ($device->device_type ?? '-') . '</td>';
        echo '<td class="px-2 py-2">' . ($device->device_manufacturer ?? '-') . '</td>';
        echo '<td class="px-2 py-2">' . ($device->device_model ?? '-') . '</td>';
        echo '<td class="px-2 py-2">' . ($device->device_status ?? '-') . '</td>';
        // Parent? column
        echo '<td class="px-2 py-2 text-center">' . ($isParent ? '<span class="text-green-600 font-bold">Yes</span>' : '<span class="text-gray-400">No</span>') . '</td>';
        echo '</tr>';
        // Only show children if parent and expanded
        if ($isParent && $hasChildren) {
            foreach ($device->assigned_devices as $child) {
                echo '<tr x-show="open" x-transition>';
                echo '<td></td>';
                echo '<td class="px-2 py-2 pl-8 cursor-pointer" onclick="window.location=\'' . url('/devices/' . (isset($child->id) ? substr($child->id, 0, 8) : (isset($child->uuid) ? substr($child->uuid, 0, 8) : '')) ) . '\'">' . (isset($child->id) ? substr($child->id, 0, 8) : (isset($child->uuid) ? substr($child->uuid, 0, 8) : 'N/A')) . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_type ?? '-') . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_manufacturer ?? '-') . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_model ?? '-') . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_status ?? '-') . '</td>';
                echo '<td class="px-2 py-2 text-center">' . (($child->parent_device ?? false) ? '<span class="text-green-600 font-bold">Yes</span>' : '<span class="text-gray-400">No</span>') . '</td>';
                echo '</tr>';
                // Recursively render grandchildren (if any)
                if (!empty($child->assigned_devices)) {
                    __device_row_blade($child, $level + 1, $rowKey . '-');
                }
            }
        }
    }
    @endphp
@endif

<div class="mb-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Devices by Controller & Feed</h3>
        @if (!$plant)
            <div class="text-red-500 italic mb-4">Error loading plant data. Please try again later.</div>
        @elseif ($plant->controllers && count($plant->controllers))
            @foreach ($plant->controllers as $controller)
                <div class="mb-6 border-2 border-indigo-200 rounded-lg p-4 bg-indigo-50">
                    <div class="mb-2 flex items-center justify-between">
                        <h4 class="text-xl font-bold text-indigo-800">Controller #{{ $controller->uid ?? $controller->id ?? 'N/A' }} <span class="text-xs text-gray-500">( CONTROLLER)</span></h4>
                        <span class="text-xs text-gray-500">Serial No: {{ $controller->serial_number ?? $controller->id ?? 'N/A' }}</span>
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
                                            <th class="w-10"></th>
                                            <th class="px-2 py-2 text-left font-semibold">ID</th>
                                            <th class="px-2 py-2 text-left font-semibold">Type</th>
                                            <th class="px-2 py-2 text-left font-semibold">Manufacturer</th>
                                            <th class="px-2 py-2 text-left font-semibold">Model</th>
                                            <th class="px-2 py-2 text-left font-semibold">Status</th>
                                            <th class="px-2 py-2 text-left font-semibold">Parent ?</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (!empty($feed->devices))
                                            @foreach ($feed->devices as $device)
                                                @php __device_row_blade($device, 0); @endphp
                                            @endforeach
                                        @else
                                            <tr><td colspan="7" class="text-center text-gray-400 italic">No devices found for this feed.</td></tr>
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


