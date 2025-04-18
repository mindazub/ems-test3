<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Project Details') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-back-transition">
                <i class="bi bi-arrow-left"></i> Back to Projects
            </a>
        </div>
    </x-slot>

    <div id="page-content" class="fade-in py-12">
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
                                    <th>Activity</th>
                                    <td>
                                        @php
                                            $progress =
                                                (((int) ($project->companies_count > 0) +
                                                    (int) ($project->plants_count > 0) +
                                                    (int) ($project->devices_count > 0)) /
                                                    3) *
                                                5;
                                        @endphp

                                        <div class="text-warning d-flex align-items-center flex-wrap gap-2">
                                            {{-- Star rating --}}
                                            @for ($s = 1; $s <= 5; $s++)
                                                <i class="bi {{ $s <= $progress ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor

                                            {{-- Business icons with random badge numbers --}}
                                            @php
                                                $businessIcons = [
                                                    'briefcase',
                                                    'bar-chart',
                                                    'clipboard-data',
                                                    'graph-up',
                                                    'people',
                                                    'building',
                                                    'gear',
                                                    'shield',
                                                    'check-circle',
                                                    'exclamation-circle',
                                                    'info-circle',
                                                    'question-circle',
                                                    'lightbulb',
                                                    'trophy',
                                                    'calendar',
                                                    'clock',
                                                    'file-earmark-text',
                                                    'file-earmark-check',
                                                    'file-earmark-x',
                                                    'file-earmark-lock',
                                                    'file-earmark-lock2',
                                                ];
                                            @endphp

                                            @for ($i = 0; $i < 10; $i++)
                                                @php
                                                    $icon = $businessIcons[array_rand($businessIcons)];
                                                    $randomCount = rand(1, 99);
                                                @endphp
                                                <div class="position-relative d-inline-block">
                                                    <i class="bi bi-{{ $icon }} fs-5 text-primary"></i>
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                        style="font-size: 0.65rem;">
                                                        {{ $randomCount }}
                                                    </span>
                                                </div>
                                            @endfor
                                        </div>

                                    </td>
                                </tr>
                            @endif

                        @endauth


                    </table>

                    {{-- Project Plant and Device List with reveal toggle --}}
                    <div class="card mb-5">
                        <div class="card-body">
                            <h4 class="mb-4">Project Plant and Device List</h4>
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered table-sm table-hover align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 25%;">Project</th>
                                            <th style="width: 25%;">Company</th>
                                            <th style="width: 25%;">Plant</th>
                                            <th style="width: 25%;">Device</th>
                                        </tr>
                                    </thead>
                                    <tbody id="device-table-body">
                                        @php $rowCount = 0; @endphp
                                        @foreach ($project->companies as $company)
                                            @foreach ($company->plants as $plant)
                                                @php $deviceCount = $plant->devices->count(); @endphp

                                                @if ($deviceCount === 0)
                                                    @php $rowCount++; @endphp
                                                    <tr
                                                        class="device-row {{ $rowCount > 5 ? 'd-none more-row' : '' }}">
                                                        <td>{{ $project->name }}</td>
                                                        <td>{{ $company->name }}</td>
                                                        <td>{{ $plant->name }}</td>
                                                        <td class="text-muted">No devices</td>
                                                    </tr>
                                                @else
                                                    @foreach ($plant->devices as $device)
                                                        @php $rowCount++; @endphp
                                                        <tr
                                                            class="device-row {{ $rowCount > 5 ? 'd-none more-row' : '' }}">
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
                            </div>

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





                    @auth

                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                            <div class="mt-4 d-flex">
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary me-2">Edit
                                    Project</a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this project?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">Delete Project</button>
                                </form>
                            </div>
                        @endif

                    @endauth



                </div>
            </div>

            {{-- Battery/Tariff and Energy Graphs Section --}}
            <div class="mb-5">
                {{-- Energy Live Chart Section --}}
                <div class="card mb-5">
                    <div class="card-header pb-0">
                        <ul class="nav nav-tabs" id="energyTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="graph-tab" data-bs-toggle="tab"
                                    data-bs-target="#graphTab" type="button" role="tab" aria-controls="graphTab"
                                    aria-selected="true">Graph</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#dataTab"
                                    type="button" role="tab" aria-controls="dataTab"
                                    aria-selected="false">Data</button>
                            </li>
                            <li class="nav-item ms-auto" role="presentation">
                                <div class="nav-link p-0 border-0 bg-transparent">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            id="energyDownloadMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="energyDownloadMenu">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartImage('energyChart', null,2)">Download PNG
                                                    (High-Res)</a>
                                            </li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartCSV('energyChart', window.energyChart)">Download
                                                    CSV</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartPDF('energyChart', 'energyDataTable')">Download
                                                    PDF</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body tab-content" id="energyTabContent" style="height: 550px;">
                        <!-- Graph Tab -->
                        <div class="tab-pane fade show active h-100" id="graphTab" role="tabpanel"
                            aria-labelledby="graph-tab">
                            <h4 class="text-center m-3">Energy Live Chart</h4>
                            <div style="height: calc(100% - 90px); display: flex; align-items: center;">
                                <canvas id="energyChart" style="width: 100%; height: 100%;"></canvas>
                            </div>
                        </div>

                        <!-- Data Tab -->
                        <div class="tab-pane fade h-100" id="dataTab" role="tabpanel" aria-labelledby="data-tab">
                            <div class="table-responsive h-100">
                                <table id="energyDataTable" class="table table-bordered table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Time</th>
                                            <th>PV (kW)</th>
                                            <th>Battery (kW)</th>
                                            <th>Grid (kW)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="energyDataTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Battery Power and Tariff --}}
                <div class="card mb-5">
                    <div class="card-header pb-0">
                        <ul class="nav nav-tabs" id="batteryTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="battery-graph-tab" data-bs-toggle="tab"
                                    data-bs-target="#batteryGraphTab" type="button" role="tab"
                                    aria-controls="batteryGraphTab" aria-selected="true">Graph</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="battery-data-tab" data-bs-toggle="tab"
                                    data-bs-target="#batteryDataTab" type="button" role="tab"
                                    aria-controls="batteryDataTab" aria-selected="false">Data</button>
                            </li>
                            <li class="nav-item ms-auto" role="presentation">
                                <div class="nav-link p-0 border-0 bg-transparent">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            type="button" id="batteryDownloadMenu" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="batteryDownloadMenu">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartImage('batteryChart')">Download PNG</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartCSV('batteryChart', window.batteryChart)">Download
                                                    CSV</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartPDF('batteryChart', 'batteryDataTable')">Download
                                                    PDF</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body tab-content" id="batteryTabContent" style="height: 550px;">
                        <!-- Graph Tab -->
                        <div class="tab-pane fade show active h-100" id="batteryGraphTab" role="tabpanel"
                            aria-labelledby="battery-graph-tab">
                            <h4 class="text-center m-3">Battery Power and Tariff</h4>
                            <div style="height: calc(100% - 90px); display: flex; align-items: center;">
                                <canvas id="batteryChart" style="width: 100%; height: 100%;"></canvas>
                            </div>
                        </div>

                        <!-- Data Tab -->
                        <div class="tab-pane fade h-100" id="batteryDataTab" role="tabpanel"
                            aria-labelledby="battery-data-tab">
                            <div class="table-responsive h-100">
                                <table id="batteryDataTable" class="table table-bordered table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Time</th>
                                            <th>Battery Power (W)</th>
                                            <th>Tariff (€ / kWh)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="batteryDataTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Battery Savings --}}
                <div class="card mb-5">
                    <div class="card-header pb-0">
                        <ul class="nav nav-tabs" id="batterySavingsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="batterySavings-graph-tab" data-bs-toggle="tab"
                                    data-bs-target="#batterySavingsGraphTab" type="button" role="tab"
                                    aria-controls="batterySavingsGraphTab" aria-selected="true">Graph</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="batterySavings-data-tab" data-bs-toggle="tab"
                                    data-bs-target="#batterySavingsDataTab" type="button" role="tab"
                                    aria-controls="batterySavingsDataTab" aria-selected="false">Data</button>
                            </li>
                            <li class="nav-item ms-auto" role="presentation">
                                <div class="nav-link p-0 border-0 bg-transparent">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            type="button" id="batterySavingsDownloadMenu" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="batterySavingsDownloadMenu">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartImage('batterySavingsChart')">Download
                                                    PNG</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartCSV('batterySavingsChart', window.batterySavingsChart)">Download
                                                    CSV</a></li>

                                            <li><a class="dropdown-item" href="#"
                                                    onclick="downloadChartPDF('batterySavingsChart', 'batterySavingsDataTable')">Download
                                                    PDF</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body tab-content" id="batterySavingsTabContent" style="height: 550px;">
                        <!-- Graph Tab -->
                        <div class="tab-pane fade show active h-100" id="batterySavingsGraphTab" role="tabpanel"
                            aria-labelledby="batterySavings-graph-tab">
                            <h4 class="text-center m-3">Battery Savings</h4>
                            <p id="batteryEarningDisplay" class="text-center fw-bold animate-flash">Total Earnings:
                                calculating...</p>
                            <div style="height: calc(100% - 90px); display: flex; align-items: center;">
                                <canvas id="batterySavingsChart" style="width: 100%; height: 100%;"></canvas>
                            </div>
                        </div>

                        <!-- Data Tab -->
                        <div class="tab-pane fade h-100" id="batterySavingsDataTab" role="tabpanel"
                            aria-labelledby="batterySavings-data-tab">
                            <div class="table-responsive h-100">
                                <table id="batterySavingsDataTable" class="table table-bordered table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Time</th>
                                            <th>Battery Savings (€)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="batterySavingsDataTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Project Breakdown Charts --}}
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header">Bar Chart</div>
                        <div class="card-body">
                            <canvas id="barChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header">Pie Chart</div>
                        <div class="card-body">
                            <canvas id="pieChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header">Line Chart</div>
                        <div class="card-body">
                            <canvas id="lineChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- External Chart Script and Logic --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const dataCounts = {
            companies: {{ $project->companies_count }},
            plants: {{ $project->plants_count }},
            devices: {{ $project->devices_count }},
        };

        const labels = ['Companies', 'Plants', 'Devices'];
        const chartData = [dataCounts.companies, dataCounts.plants, dataCounts.devices];

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
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

        // Charts from batteries_ok.json and battery_savings.json
        fetch("{{ asset('batteries_ok.json') }}")
            .then(res => res.json())
            .then(data => {
                const entries = Object.entries(data).sort(([a], [b]) => Number(a) - Number(b));
                const labels = entries.map(([ts]) => new Date(ts).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                const pvData = entries.map(([, val]) => val.pv_p);
                const batteryData = entries.map(([, val]) => val.battery_p);
                const gridData = entries.map(([, val]) => val.grid_p);
                const tariffData = entries.map(([, val]) => val.tariff);

                window.energyChart = new Chart(document.getElementById('energyChart'), {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                                label: 'PV Production (kW)',
                                data: pvData,
                                borderColor: 'rgba(59,130,246,1)',
                                backgroundColor: 'rgba(59,130,246,0.2)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 0
                            },
                            {
                                label: 'Grid Power (kW)',
                                data: gridData,
                                borderColor: 'rgba(34,197,94,1)',
                                backgroundColor: 'rgba(34,197,94,0.2)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 0
                            },
                            {
                                label: 'Battery Power (kW)',
                                data: batteryData,
                                borderColor: 'rgba(249,115,22,1)',
                                backgroundColor: 'rgba(249,115,22,0.2)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        stacked: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time',
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                ticks: {
                                    color: '#000',
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    color: '#ccc',
                                    lineWidth: 1.5
                                },
                                border: {
                                    display: true,
                                    color: '#000',
                                    width: 2
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Power (kW)',
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                ticks: {
                                    color: '#000'
                                },
                                grid: {
                                    color: '#ccc',
                                    lineWidth: 1.5
                                },
                                border: {
                                    display: true,
                                    color: '#000',
                                    width: 2
                                }
                            }
                        }
                    }
                });


                const tableBody = document.getElementById('energyDataTableBody');
                if (tableBody) {
                    entries.slice().reverse().forEach(([ts, val]) => {
                        const row = document.createElement('tr');
                        const date = new Date(isNaN(ts) ? ts : Number(ts)).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        row.innerHTML = `
            <td>${date}</td>
            <td>${val.pv_p.toFixed(2)}</td>
            <td>${val.battery_p.toFixed(2)}</td>
            <td>${val.grid_p.toFixed(2)}</td>
        `;
                        tableBody.appendChild(row);
                    });
                }




                const batteryDataBody = document.getElementById('batteryDataTableBody');
                if (batteryDataBody) {
                    entries.slice().reverse().forEach(([ts, val]) => {
                        const row = document.createElement('tr');
                        const time = new Date(isNaN(ts) ? ts : Number(ts)).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        row.innerHTML = `
            <td>${time}</td>
            <td>${val.battery_p.toFixed(2)}</td>
            <td>${val.tariff.toFixed(4)}</td>
        `;
                        batteryDataBody.appendChild(row);
                    });
                }


                window.batteryChart = new Chart(document.getElementById('batteryChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                                type: 'line',
                                label: 'Battery Power (W)',
                                data: batteryData,
                                borderColor: 'blue',
                                backgroundColor: 'rgba(0,0,255,0.1)',
                                yAxisID: 'yBattery',
                                tension: 0.2,
                                pointRadius: 0
                            },
                            {
                                type: 'bar',
                                label: 'Energy Tariffs (€ / kWh)',
                                data: tariffData,
                                backgroundColor: 'rgba(0,128,0,0.5)',
                                borderColor: 'green',
                                yAxisID: 'yTariff'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            yBattery: {
                                type: 'linear',
                                position: 'left',
                                suggestedMin: -15000,
                                suggestedMax: 15000,
                                title: {
                                    display: true,
                                    text: 'Battery Power (W)',
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                ticks: {
                                    color: 'blue'
                                },
                                grid: {
                                    color: '#ccc',
                                    lineWidth: 1.5
                                },
                                border: {
                                    display: true,
                                    color: 'blue',
                                    width: 2
                                }
                            },
                            yTariff: {
                                type: 'linear',
                                position: 'right',
                                suggestedMin: -0.25,
                                suggestedMax: 0.25,
                                title: {
                                    display: true,
                                    text: 'Energy Tariffs (€ / kWh)',
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                ticks: {
                                    color: 'green'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                border: {
                                    display: true,
                                    color: 'green',
                                    width: 2
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time',
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                ticks: {
                                    color: '#000',
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    color: '#ccc',
                                    lineWidth: 1.5
                                },
                                border: {
                                    display: true,
                                    color: '#000',
                                    width: 2
                                }
                            }
                        }

                    }
                });
            });

        let batterySavingsChart;

        fetch("{{ asset('battery_savings.json') }}")
            .then(res => res.json())
            .then(data => {
                const entries = Object.entries(data).sort(([a], [b]) => new Date(a) - new Date(b));

                const labels = entries.map(([ts]) =>
                    new Date(ts).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                );

                const savings = entries.map(([, val]) => val.battery_savings);
                const colors = savings.map(val =>
                    val >= 0 ? 'rgba(34,197,94,0.7)' : 'rgba(239,68,68,0.7)'
                );

                const total = savings.reduce((sum, val) => sum + val, 0);
                document.getElementById('batteryEarningDisplay').innerText = `Total Earnings: €${total.toFixed(2)}`;

                // Fill the data table
                const tableBody = document.getElementById('batterySavingsDataTableBody');
                if (tableBody) {
                    entries.slice().reverse().forEach(([ts, val]) => {
                        const row = document.createElement('tr');
                        const time = new Date(ts).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        row.innerHTML = `
                    <td>${time}</td>
                    <td>€${val.battery_savings.toFixed(2)}</td>
                `;
                        tableBody.appendChild(row);
                    });
                }

                // Chart

                window.batterySavingsChart = new Chart(document.getElementById('batterySavingsChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Battery Savings (€)',
                            data: savings,
                            backgroundColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `€${context.parsed.y.toFixed(2)}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Savings (€)',
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                ticks: {
                                    color: '#000'
                                },
                                grid: {
                                    color: '#ccc',
                                    lineWidth: ctx => ctx.tick.value === 0 ? 4 : 1.5
                                },
                                border: {
                                    display: true,
                                    color: '#000',
                                    width: 2
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time',
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                ticks: {
                                    color: '#000',
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    color: '#ccc',
                                    lineWidth: 1.5
                                },
                                border: {
                                    display: true,
                                    color: '#000',
                                    width: 2
                                }
                            }
                        }
                    }
                });
            });
    </script>

    <style>
        .fade-in-row {
            opacity: 0;
            height: 0;
            transition: opacity 0.4s ease-in-out, height 0.4s ease-in-out;
            overflow: hidden;
        }

        .fade-in-row.show {
            opacity: 1;
            height: auto;
        }
    </style>

    <script>
        const revealButton = document.getElementById('reveal-button');
        const collapseButton = document.getElementById('collapse-button');

        revealButton?.addEventListener('click', function() {
            document.querySelectorAll('.more-row').forEach((row, index) => {
                setTimeout(() => {
                    row.classList.remove('d-none');
                    row.classList.add('show');
                }, index * 50); // 100ms delay between each row
            });
            revealButton.classList.add('d-none');
            collapseButton.classList.remove('d-none');
        });

        collapseButton?.addEventListener('click', function() {
            const rows = document.querySelectorAll('.more-row');

            rows.forEach((row, index) => {
                setTimeout(() => {
                    row.classList.remove('show');
                    setTimeout(() => {
                        row.classList.add('d-none');
                    }, 400); // matches the CSS transition time
                }, index * 50); // stagger delay
            });

            revealButton.classList.remove('d-none');
            collapseButton.classList.add('d-none');
        });
    </script>

    <!-- Chart Download Utilities -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        // Download chart image as PNG
        function downloadChartImage(chartId, chartTitle = null, scale = 2) {
            const canvas = document.getElementById(chartId);
            if (!canvas) return;

            const chartInstance = Chart.getChart(canvas); // Get Chart.js instance
            if (!chartInstance) return;

            const originalConfig = chartInstance.config;
            const width = canvas.offsetWidth;
            const height = canvas.offsetHeight;

            // Create a new off-screen canvas
            const exportCanvas = document.createElement("canvas");
            exportCanvas.width = width * scale;
            exportCanvas.height = height * scale;

            // Create a new Chart on the off-screen canvas
            new Chart(exportCanvas.getContext("2d"), {
                type: chartInstance.config.type,
                data: chartInstance.config.data,
                options: {
                    ...chartInstance.config.options,
                    responsive: false,
                    animation: false,
                    devicePixelRatio: scale,
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 7 * scale // upscale font for clarity
                                }
                            }
                        }
                    }
                }
            });

            // Add custom title
            const tempCanvas = document.createElement("canvas");
            const paddingTop = 80;
            tempCanvas.width = exportCanvas.width;
            tempCanvas.height = exportCanvas.height + paddingTop;
            const tempCtx = tempCanvas.getContext("2d");

            tempCtx.fillStyle = "#ffffff";
            tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

            chartTitle = chartTitle || canvas.closest('.tab-pane')?.querySelector('h4')?.textContent.trim() || 'Chart';

            tempCtx.fillStyle = "#000";

            // Make title font bigger and bolder
            const titleFontSize = 28 * scale;
            tempCtx.font = `bold ${titleFontSize}px Arial`;
            tempCtx.textAlign = "center";

            // Adjust Y position to match new font size
            tempCtx.fillText(chartTitle, tempCanvas.width / 2, titleFontSize + 10);

            // Draw the chart below the title (leave enough space)
            tempCtx.drawImage(exportCanvas, 0, titleFontSize + 30);

            // Trigger download
            const image = tempCanvas.toDataURL("image/png");
            const link = document.createElement("a");
            link.href = image;
            link.download = `${chartId}_hires.png`;
            link.click();
        }






        // Download chart data as CSV
        function downloadChartCSV(chartId, chartInstance) {
            if (!chartInstance || !chartInstance.data || !chartInstance.data.labels) {
                console.error(`Chart instance for ${chartId} not found or invalid.`);
                return;
            }

            // Try to find title from surrounding h4
            let chartTitle = 'Chart Data';
            const titleEl = document.getElementById(chartId)?.closest('.tab-pane')?.querySelector('h4');
            if (titleEl) {
                chartTitle = titleEl.textContent.trim();
            }

            let csv = `${chartTitle}\n`;
            csv += 'Time';
            chartInstance.data.datasets.forEach(dataset => {
                csv += `,${dataset.label}`;
            });
            csv += '\n';

            chartInstance.data.labels.forEach((label, index) => {
                let row = `"${label}"`; // in case label has commas
                chartInstance.data.datasets.forEach(dataset => {
                    const value = dataset.data[index] !== undefined ? dataset.data[index] : '';
                    row += `,"${value}"`;
                });
                csv += row + '\n';
            });

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');

            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `${chartId}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }




        // Download chart + data table as PDF


        function downloadChartPDF(chartId, tableId = null, scale = 3) {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            const canvas = document.getElementById(chartId);
            if (!canvas) return;

            const chartInstance = Chart.getChart(canvas);
            if (!chartInstance) return;

            const width = canvas.offsetWidth;
            const height = canvas.offsetHeight;

            // Create high-res off-screen canvas
            const exportCanvas = document.createElement("canvas");
            exportCanvas.width = width * scale;
            exportCanvas.height = height * scale;
            const exportCtx = exportCanvas.getContext("2d");

            // Deep copy chart options and enhance them
            const enhancedOptions = JSON.parse(JSON.stringify(chartInstance.config.options));

            enhancedOptions.responsive = false;
            enhancedOptions.animation = false;
            enhancedOptions.devicePixelRatio = scale;

            // Improve legend font
            if (!enhancedOptions.plugins) enhancedOptions.plugins = {};
            if (!enhancedOptions.plugins.legend) enhancedOptions.plugins.legend = {};
            if (!enhancedOptions.plugins.legend.labels) enhancedOptions.plugins.legend.labels = {};
            enhancedOptions.plugins.legend.labels.font = {
                size: 20 * scale
            };

            // Enhance axis settings
            enhancedOptions.scales = enhancedOptions.scales || {};

            enhancedOptions.scales.x = {
                title: {
                    display: true,
                    text: 'Time',
                    color: '#000',
                    font: {
                        size: 22 * scale,
                        weight: 'bold'
                    }
                },
                ticks: {
                    callback: function(value, index, ticks) {
                        return index % 6 === 0 ? this.getLabelForValue(value) : '';
                    },
                    font: {
                        size: 18 * scale
                    },
                    color: '#000'
                },
                grid: {
                    color: '#ccc',
                    lineWidth: 1.5
                },
                border: {
                    display: true,
                    color: '#000',
                    width: 2
                }
            };

            enhancedOptions.scales.y = {
                title: {
                    display: true,
                    text: 'Power (kW)',
                    color: '#000',
                    font: {
                        size: 22 * scale,
                        weight: 'bold'
                    }
                },
                ticks: {
                    font: {
                        size: 18 * scale
                    },
                    color: '#000'
                },
                grid: {
                    color: '#ccc',
                    lineWidth: 1.5
                },
                border: {
                    display: true,
                    color: '#000',
                    width: 2
                }
            };

            // Render chart to off-screen canvas
            new Chart(exportCtx, {
                type: chartInstance.config.type,
                data: chartInstance.config.data,
                options: enhancedOptions
            });

            const imageData = exportCanvas.toDataURL("image/png");

            // Chart title
            let chartTitle = "Chart Snapshot";
            const titleEl = canvas.closest(".tab-pane")?.querySelector("h4");
            if (titleEl) chartTitle = titleEl.textContent.trim();

            // PDF title
            doc.setFontSize(22);
            doc.setFont("helvetica", "bold");
            doc.text(chartTitle, 105, 15, {
                align: "center"
            });

            // Insert chart image
            const imgWidth = 180;
            const aspectRatio = exportCanvas.height / exportCanvas.width;
            const imgHeight = imgWidth * aspectRatio;
            doc.addImage(imageData, "PNG", 15, 25, imgWidth, imgHeight);

            let currentY = 30 + imgHeight;

            // Add table if provided
            if (tableId) {
                const table = document.getElementById(tableId);
                if (table) {
                    const headers = [...table.querySelectorAll("thead th")].map(th => th.innerText);
                    const body = [...table.querySelectorAll("tbody tr")].map(row => [...row.querySelectorAll("td")].map(
                        td => td.innerText));

                    doc.autoTable({
                        startY: currentY + 10,
                        head: [headers],
                        body: body,
                        theme: "grid",
                        styles: {
                            fontSize: 9,
                            cellPadding: 3
                        }
                    });
                }
            }

            doc.save(`${chartId}_report.pdf`);
        }
    </script>


    <!-- Include AutoTable plugin for jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>





</x-app-layout>
