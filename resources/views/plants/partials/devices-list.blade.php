@if (!function_exists('__device_row_blade'))
    @php
    function __device_row_blade($device, $level = 0, $parentKey = '') {
        // Determine if device is parent: has assigned_devices and parameters does not have slave_id
        $hasChildren = !empty($device->assigned_devices);
        $parameters = (array)($device->parameters ?? []);
        $isParent = $hasChildren && !array_key_exists('slave_id', $parameters);
        $rowKey = $parentKey . ($device->id ?? $device->uid ?? uniqid());
        echo '<tr x-data="{ open: false }" data-device-id="' . ($device->id ?? $device->uid ?? '') . '">';
        // Expand/Collapse Icon
        echo '<td class="w-10 text-center">';
        if ($isParent) {
            echo '<button type="button" class="focus:outline-none" @click="open = !open" :aria-expanded="open" aria-label="Toggle children">';
            echo '<template x-if="!open"><x-heroicon-o-plus class="w-5 h-5 text-indigo-600 inline" /></template>';
            echo '<template x-if="open"><x-heroicon-o-minus class="w-5 h-5 text-indigo-600 inline" /></template>';
            echo '</button>';
        } else {
            echo $level > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '↳' : '';
        }
        echo '</td>';
        // ID columns: trimmed for display, full for routing
        $deviceId = $device->id ?? $device->uid ?? '';
        $fullId = $device->uid ?? $device->id ?? '';
        $shortId = substr($deviceId, 0, 8);
        echo '<td class="px-2 py-2 cursor-pointer">';
        echo '<a href="' . url('/devices/' . $fullId) . '" class="text-blue-600 hover:underline" title="' . $fullId . '">' . $shortId . '</a>';
        echo '</td>';
        // Device info columns
        echo '<td class="px-2 py-2">' . ($device->device_type ?? '-') . '</td>';
        echo '<td class="px-2 py-2">' . ($device->device_manufacturer ?? '-') . '</td>';
        echo '<td class="px-2 py-2">' . ($device->device_model ?? '-') . '</td>';
        echo '<td class="px-2 py-2">' . ($device->device_status ?? '-') . '</td>';
        // Assigned To column
        echo '<td class="px-2 py-2">';
        if (!empty($device->parent_device_id)) {
            $parentFullId = $device->parent_device_id;
            $parentShortId = substr($parentFullId, 0, 8);
            echo '<a href="' . url('/devices/' . $parentFullId) . '" class="text-blue-600 hover:underline" title="' . $parentFullId . '">' . $parentShortId . '</a>';
        } else {
            echo '—';
        }
        echo '</td>';
        // Parent? column
        echo '<td class="px-2 py-2 text-center">' . ($isParent ? '<span class="text-green-600 font-bold">Yes</span>' : '<span class="text-gray-400">No</span>') . '</td>';
        echo '</tr>';
        // Only show children if parent and expanded
        if ($isParent && $hasChildren) {
            foreach ($device->assigned_devices as $child) {
                echo '<tr x-show="open" x-transition>';
                echo '<td></td>';
                $childId = isset($child->id) ? $child->id : (isset($child->uid) ? $child->uid : '');
                $childFullId = isset($child->uid) ? $child->uid : (isset($child->id) ? $child->id : '');
                $childShortId = substr($childId, 0, 8);
                echo '<td class="px-2 py-2 pl-8 cursor-pointer" onclick="window.location=\'' . url('/devices/' . $childFullId) . '\'" title="' . $childFullId . '">' . ($childShortId ?: 'N/A') . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_type ?? '-') . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_manufacturer ?? '-') . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_model ?? '-') . '</td>';
                echo '<td class="px-2 py-2">' . ($child->device_status ?? '-') . '</td>';
                // Assigned To column for child
                echo '<td class="px-2 py-2">';
                if (!empty($child->parent_device_id)) {
                    $parentFullId = $child->parent_device_id;
                    $parentShortId = substr($parentFullId, 0, 8);
                    echo '<a href="' . url('/devices/' . $parentFullId) . '" class="text-blue-600 hover:underline" title="' . $parentFullId . '">' . $parentShortId . '</a>';
                } else {
                    echo '—';
                }
                echo '</td>';
                // Recursively check if child is parent
                $childParams = (array)($child->parameters ?? []);
                $childIsParent = !empty($child->assigned_devices) && !array_key_exists('slave_id', $childParams);
                echo '<td class="px-2 py-2 text-center">' . ($childIsParent ? '<span class="text-green-600 font-bold">Yes</span>' : '<span class="text-gray-400">No</span>') . '</td>';
                echo '</tr>';
                // Recursively render grandchildren (if any)
                if ($childIsParent && !empty($child->assigned_devices)) {
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
                                            <th class="px-2 py-2 text-left font-semibold">Assigned To</th>
                                            <th class="px-2 py-2 text-left font-semibold">Parent ?</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (!empty($feed->devices))
                                            @foreach ($feed->devices as $device)
                                                @php __device_row_blade($device, 0); @endphp
                                            @endforeach
                                        @else
                                            <tr><td colspan="8" class="text-center text-gray-400 italic">No devices found for this feed.</td></tr>
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


