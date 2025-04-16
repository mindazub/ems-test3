<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Project Details') }}
            </h2>
            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">← Back to Projects</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container">
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
                            <td>{{ $project->companies_count ?? $project->companies->count() }}</td>
                        </tr>
                        <tr>
                            <th>Plants</th>
                            <td>{{ $project->plants_count ?? $project->plants->count() }}</td>
                        </tr>
                        <tr>
                            <th>Devices</th>
                            <td>{{ $project->devices_count ?? $project->devices->count() }}</td>
                        </tr>
                        <tr>
                            <th>Progress</th>
                            <td>
                                @php
                                    $progress =
                                        (round(
                                            ($project->companies_count > 0 ? 1 : 0) +
                                                ($project->plants_count > 0 ? 1 : 0) +
                                                ($project->devices_count > 0 ? 1 : 0),
                                        ) /
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
                    </table>

                    <div class="mt-4 d-flex">
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary me-2">Edit Project</a>

                        <form action="{{ route('projects.destroy', $project) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this project?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Delete Project</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ✅ Chart Section --}}
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">Bar Chart</div>
                        <div class="card-body">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">Pie Chart</div>
                        <div class="card-body">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">Line Chart</div>
                        <div class="card-body">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Bootstrap + Chart.js CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const dataCounts = {
            companies: {{ $project->companies_count ?? $project->companies->count() }},
            plants: {{ $project->plants_count ?? $project->plants->count() }},
            devices: {{ $project->devices_count ?? $project->devices->count() }},
        };

        const labels = ['Companies', 'Plants', 'Devices'];
        const chartData = [dataCounts.companies, dataCounts.plants, dataCounts.devices];

        const chartOptions = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true
                }
            }
        };

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Total Count',
                    data: chartData,
                    backgroundColor: ['#0d6efd', '#198754', '#dc3545']
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    label: 'Total Count',
                    data: chartData,
                    backgroundColor: ['#0d6efd', '#198754', '#dc3545']
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Total Count',
                    data: chartData,
                    fill: false,
                    borderColor: '#0d6efd',
                    tension: 0.3
                }]
            },
            options: chartOptions
        });
    </script>
</x-app-layout>
