<div class="mb-6">
    <div class="card mb-5">
        <div class="card-header pb-0">
            <h3 class="text-lg font-semibold mb-4">Devices by Feed</h3>
        </div>
        <div class="card-body">
            @foreach ($plant->mainFeeds as $feed)
                <div class="mb-4 border rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="font-semibold text-primary">Main Feed ID: {{ $feed->id }}</h5>
                        <button onclick="window.print()" class="btn btn-sm btn-success">
                            <i class="bi bi-printer"></i> Export PDF
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Manufacturer</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                    <th>Parent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feed->devices->where('parent_device', true) as $parent)
                                    @php $parentKey = 'parent-' . $parent->id; @endphp
                                    <tr class="bg-light">
                                        <td>{{ $parent->device_type }}</td>
                                        <td>{{ $parent->manufacturer }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-link p-0 toggle-btn" type="button"
                                                data-toggle="collapse-row" data-target="#{{ $parentKey }}">
                                                <i class="bi bi-caret-right-fill toggle-icon"></i>
                                                {{ $parent->device_model }}
                                            </button>
                                        </td>
                                        <td>{{ $parent->device_status }}</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr class="slave-row" id="{{ $parentKey }}" style="display: none">
                                        <td colspan="5" class="p-0">
                                            <table class="table table-hover mb-0">
                                                <tbody>
                                                    @foreach ($feed->devices->where('parent_device', false) as $child)
                                                        @if ($child->main_feed_id === $parent->main_feed_id)
                                                            <tr>
                                                                <td>{{ $child->device_type }}</td>
                                                                <td>{{ $child->manufacturer }}</td>
                                                                <td>{{ $child->device_model }}</td>
                                                                <td>{{ $child->device_status }}</td>
                                                                <td>No</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            <div class="mt-4">
                <a href="{{ route('plants.index') }}" class="btn btn-secondary">Back to Plants</a>
            </div>
        </div>
    </div>
</div>
