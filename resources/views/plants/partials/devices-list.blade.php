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
                                    <th></th>
                                    <th>Type</th>
                                    <th>Manufacturer</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feed->devices->where('parent_device', true) as $parent)
                                    @php $parentKey = 'parent-' . $parent->id; @endphp
                                    <tr class="bg-light parent-row clickable-row" style="cursor: pointer"
                                        onclick="window.location='{{ url('/devices/' . $parent->id) }}'">
                                        <td class="text-center">
                                            <button class="btn btn-sm p-0 toggle-btn" type="button"
                                                data-toggle="collapse-row" data-target="#{{ $parentKey }}"
                                                onclick="event.stopPropagation();">
                                                <i class="bi bi-plus-circle toggle-icon"></i>
                                            </button>
                                        </td>
                                        <td>{{ $parent->device_type }}</td>
                                        <td>{{ $parent->manufacturer }}</td>
                                        <td>{{ $parent->device_model }}</td>
                                        <td>{{ $parent->device_status }}</td>
                                    </tr>
                                    <tr class="slave-row" id="{{ $parentKey }}" style="display: none">
                                        <td colspan="5" class="p-0">
                                            <table class="table table-hover mb-0">
                                                <tbody>
                                                    @foreach ($feed->devices->where('parent_device', false) as $child)
                                                        @if ($child->main_feed_id === $parent->main_feed_id)
                                                            <tr class="clickable-row" style="cursor: pointer"
                                                                onclick="window.location='{{ url('/devices/' . $child->id) }}'">
                                                                <td class="text-center"><i
                                                                        class="bi bi-arrow-right"></i></i>
                                                                </td>
                                                                <td>{{ $child->device_type }}</td>
                                                                <td>{{ $child->manufacturer }}</td>
                                                                <td>{{ $child->device_model }}</td>
                                                                <td>{{ $child->device_status }}</td>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-btn').forEach(button => {
            button.addEventListener('click', (event) => {
                const targetId = button.getAttribute('data-target');
                const row = document.querySelector(targetId);
                const icon = button.querySelector('.toggle-icon');

                if (row.style.display === 'none') {
                    row.style.display = 'table-row';
                    icon.classList.remove('bi-plus-circle');
                    icon.classList.add('bi-dash-circle');
                } else {
                    row.style.display = 'none';
                    icon.classList.remove('bi-dash-circle');
                    icon.classList.add('bi-plus-circle');
                }
            });
        });
    });
</script>
