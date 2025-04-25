{{-- resources/views/plants/partials/devices-list.blade.php --}}
<div class="mb-6">
    <div class="card mb-5">
        <div class="card-header pb-0">
            <h3 class="text-lg font-semibold mb-4">Devices by Feed</h3>
        </div>
        <div class="card-body">
            @if ($plant->mainFeeds->isEmpty())
                <p class="text-muted">No main feeds found for this plant.</p>
            @else
                @foreach ($plant->mainFeeds as $feed)
                    <div class="mb-4 border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="font-semibold text-primary">Main Feed ID: {{ $feed->id }}</h5>
                            {{-- Consider making print functionality more robust if needed --}}
                            <button onclick="window.print()" class="btn btn-sm btn-success">
                                <i class="bi bi-printer"></i> Export PDF
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 1%;"></th> {{-- Toggle Column --}}
                                        <th>Type</th>
                                        <th>Manufacturer</th>
                                        <th>Model</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop through parent devices for this feed --}}
                                    @forelse ($feed->devices->where('parent_device', true) as $parent)
                                        @php $parentKey = 'parent-' . $feed->id . '-' . $parent->id; @endphp {{-- More unique key --}}
                                        {{-- Parent Row --}}
                                        <tr class="bg-light">
                                            {{-- Toggle Button Column --}}
                                            <td>
                                                <a href="#" class="btn btn-link p-0 toggle-btn"
                                                    {{-- toggle-btn class kept for potential styling --}} type="button" data-bs-toggle="collapse"
                                                    {{-- Bootstrap 5 toggle attribute --}} data-bs-target="#{{ $parentKey }}"
                                                    {{-- Bootstrap 5 target attribute --}} aria-expanded="false" {{-- Initial state: collapsed --}}
                                                    aria-controls="{{ $parentKey }}"> {{-- Accessibility --}}
                                                    <i class="bi bi-plus-circle-fill toggle-icon"></i>
                                                    {{-- Icon to be toggled by JS --}}
                                                </a>
                                            </td>
                                            {{-- Other parent data columns --}}
                                            <td>{{ $parent->device_type ?? 'N/A' }}</td>
                                            <td>{{ $parent->manufacturer ?? 'N/A' }}</td>
                                            <td>{{ $parent->device_model ?? 'N/A' }}</td>
                                            <td>{{ $parent->device_status ?? 'N/A' }}</td>
                                        </tr>

                                        {{-- Collapsible Row (Slave/Child Row) --}}
                                        {{-- Ensure 'collapse' class is present for Bootstrap JS. No inline display style needed. --}}
                                        <tr class="collapse slave-row" id="{{ $parentKey }}">
                                            <td colspan="5" class="p-0"> {{-- Colspan matches number of columns --}}
                                                {{-- Inner table for child devices --}}
                                                <table
                                                    class="table table-sm table-hover mb-0 border-start-0 border-end-0">
                                                    {{-- More compact table --}}
                                                    {{-- Optional inner header (uncomment if desired)
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="width: 1%;"></th>
                                                            <th>Type</th>
                                                            <th>Manufacturer</th>
                                                            <th>Model</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    --}}
                                                    <tbody>
                                                        {{-- Loop through child devices associated with this parent/feed --}}
                                                        {{-- Adjust filtering logic if needed based on your exact relationships --}}
                                                        @forelse ($feed->devices->where('parent_device', false)->where('main_feed_id', $feed->id) as $child)
                                                            {{-- Add check if child belongs to *this specific* parent if relationship exists --}}
                                                            {{-- Example: ->where('parent_id', $parent->id) --}}
                                                            <tr>
                                                                {{-- Indentation/Icon for child rows --}}
                                                                <td style="width: 1%; padding-left: 25px;"><i
                                                                        class="bi bi-arrow-return-right text-muted"></i>
                                                                </td>
                                                                <td>{{ $child->device_type ?? 'N/A' }}</td>
                                                                <td>{{ $child->manufacturer ?? 'N/A' }}</td>
                                                                <td>{{ $child->device_model ?? 'N/A' }}</td>
                                                                <td>{{ $child->device_status ?? 'N/A' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted py-2">
                                                                    No child devices found for this parent.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-2">No parent devices
                                                found for this feed.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="mt-4">
                <a href="{{ route('plants.index') }}" class="btn btn-secondary">Back to Plants</a>
            </div>
        </div>
    </div>
</div>
{{-- NO SCRIPT TAG HERE - Moved to show.blade.php --}}
