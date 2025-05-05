{{-- resources/views/plants/partials/devices-list.blade.php --}}
{{-- This is a partial – no @extends here! --}}

@pushOnce('styles')
<style>
    /* ------------- keep one rock-steady column grid ------------- */
    .table-fixed               { table-layout:fixed; width:100%; }
    .table-fixed th,
    .table-fixed td            { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    /* ------------- helpers for our show / hide logic ------------ */
    tbody tr.d-none            { display:none; }
    tbody tr:not(.d-none)      { display:table-row; }
</style>
@endPushOnce


<div class="mb-6">
    <div class="card mb-5">
        <div class="card-header pb-0">
            <h3 class="text-lg font-semibold mb-4">Devices by Feed</h3>
        </div>

        <div class="card-body">
        @foreach ($plant->mainFeeds as $feed)
            <div class="mb-4 border rounded p-3">
                {{-- === feed header === --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="font-semibold text-primary mb-0">
                        Main&nbsp;Feed&nbsp;ID:&nbsp;{{ $feed->id }}
                    </h5>

                    <button onclick="window.print()" class="btn btn-sm btn-success">
                        <i class="bi bi-printer"></i> Export PDF
                    </button>
                </div>

                {{-- === device table === --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-fixed mb-0">
                        <colgroup>
                            <col style="width:54px">   {{-- toggle / arrow --}}
                            <col style="width:10%">    {{-- ID --}}
                            <col style="width:18%">    {{-- Type --}}
                            <col style="width:27%">    {{-- Manufacturer --}}
                            <col style="width:27%">    {{-- Model --}}
                            <col style="width:27%">    {{-- Status --}}
                        </colgroup>

                        <thead class="table-light">
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Manufacturer</th>
                                <th>Model</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach ($feed->devices->where('parent_device', true) as $parent)
                            {{-- ---------- parent row ---------- --}}
                            <tr class="bg-light clickable-row"
                                style="cursor:pointer"
                                onclick="window.location='{{ url('/devices/'.$parent->id) }}'">
                                <td class="text-center">
                                    <button class="btn btn-sm p-0 toggle-btn"
                                            data-target=".child-{{ $parent->id }}"
                                            onclick="event.stopPropagation();">
                                        <i class="bi bi-plus-circle fs-5 toggle-icon"></i>
                                    </button>
                                </td>
                                <td>{{ $parent->id }} </td>
                                <td class="ps-3">{{ $parent->device_type }}</td>
                                <td>{{ $parent->manufacturer }}</td>
                                <td>{{ $parent->device_model }}</td>
                                <td>{{ $parent->device_status }}</td>
                            </tr>

                            {{-- ---------- child rows ---------- --}}
                            @foreach ($feed->devices
                                        ->where('parent_device', false)
                                        ->where('main_feed_id', $parent->main_feed_id) as $child)
                                <tr class="d-none child-{{ $parent->id }}"
                                    style="cursor:pointer"
                                    onclick="window.location='{{ url('/devices/'.$child->id) }}'">
                                    <td class="text-center ps-4">
                                        <i class="bi bi-arrow-right"></i>
                                    </td>
                                    <td>{{ $child->id }}</td>
                                    <td>{{ $child->device_type }}</td>
                                    <td>{{ $child->manufacturer }}</td>
                                    <td>{{ $child->device_model }}</td>
                                    <td>{{ $child->device_status }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

            <div class="mt-4">
                <a href="{{ route('plants.index') }}" class="btn btn-secondary">
                    Back to Plants
                </a>
            </div>
        </div>
    </div>
</div>


@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const selector = btn.dataset.target;                  // ".child-XX"
            document.querySelectorAll(selector).forEach(row =>
                row.classList.toggle('d-none')                    // show / hide
            );

            const icon = btn.querySelector('.toggle-icon');       // flip icon
            icon.classList.toggle('bi-plus-circle');
            icon.classList.toggle('bi-dash-circle');
        });
    });
});
</script>
@endPushOnce
