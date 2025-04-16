<div class="card mb-4">
    <div class="card-body">
        <h3 class="mb-4">{{ $project->name }}</h3>
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <td>{{ $project->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $project->name }}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{ $project->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Companies</th>
                <td>{{ $project->companies_count }}</td>
            </tr>
            <tr>
                <th>Plants</th>
                <td>{{ $project->plants_count }}</td>
            </tr>
            <tr>
                <th>Devices</th>
                <td>{{ $project->devices_count }}</td>
            </tr>

            @auth
                @if (auth()->user()->role === 'admin')
                    <tr>
                        <th>Progress</th>
                        <td>
                            @php
                                $progress =
                                    (((int) ($project->companies_count > 0) +
                                        (int) ($project->plants_count > 0) +
                                        (int) ($project->devices_count > 0)) /
                                        3) *
                                    5;
                            @endphp
                            <div class="text-warning">
                                @for ($s = 1; $s <= 5; $s++)
                                    <i class="bi {{ $s <= $progress ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                        </td>
                    </tr>
                @endif
            @endauth
        </table>
    </div>
</div>
