<div class="card mb-5">
    <div class="card-body">
        <h4 class="mb-4">Project Plant and Device List</h4>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Company</th>
                    <th>Plant</th>
                    <th>Device</th>
                </tr>
            </thead>
            <tbody id="device-table-body">
                @php $rowCount = 0; @endphp
                @foreach ($project->companies as $company)
                    @foreach ($company->plants as $plant)
                        @php $deviceCount = $plant->devices->count(); @endphp
                        @if ($deviceCount === 0)
                            @php $rowCount++; @endphp
                            <tr class="device-row {{ $rowCount > 5 ? 'd-none more-row' : '' }}">
                                <td>{{ $project->name }}</td>
                                <td>{{ $company->name }}</td>
                                <td>{{ $plant->name }}</td>
                                <td class="text-muted">No devices</td>
                            </tr>
                        @else
                            @foreach ($plant->devices as $device)
                                @php $rowCount++; @endphp
                                <tr class="device-row {{ $rowCount > 5 ? 'd-none more-row' : '' }}">
                                    <td>{{ $project->name }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $plant->name }}</td>
                                    <td>{{ $device->name }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>

        @if ($rowCount > 5)
            <div class="text-center mt-3">
                <button id="reveal-button" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-chevron-down"></i> Show More
                </button>
                <button id="collapse-button" class="btn btn-outline-secondary btn-sm d-none">
                    <i class="bi bi-chevron-up"></i> Show Less
                </button>
            </div>
        @endif
    </div>
</div>
