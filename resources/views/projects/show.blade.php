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
                    </table>

                    {{-- Project Plant and Device List with reveal toggle --}}
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

                            @if ($rowCount > 5)
                                <div class="text-center mt-3">
                                    <button id="reveal-button" class="btn btn-outline-primary">Show More</button>
                                    <button id="collapse-button" class="btn btn-outline-secondary d-none">Show
                                        Less</button>
                                </div>
                            @endif
                        </div>
                    </div>





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

            {{-- Battery/Tariff and Energy Graphs Section --}}
            <div class="mb-5">
                <div class="bg-white rounded shadow p-4 mb-4">
                    <h4 class="text-center">Live Energy Data</h4>
                    <canvas id="energyChart" height="100"></canvas>
                </div>
                <div class="bg-white rounded shadow p-4 mb-4">
                    <h4 class="text-center">Battery Power and Tariff</h4>
                    <canvas id="batteryChart" height="100"></canvas>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h4 class="text-center">Battery Savings</h4>
                    <p id="batteryEarningDisplay" class="text-center fw-bold animate-flash">Total Earnings:
                        calculating...</p>
                    <canvas id="batterySavingsChart" height="100"></canvas>
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
                const labels = entries.map(([ts]) => new Date(Number(ts)).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                const pvData = entries.map(([, val]) => val.pv_p);
                const batteryData = entries.map(([, val]) => val.battery_p);
                const gridData = entries.map(([, val]) => val.grid_p);
                const tariffData = entries.map(([, val]) => val.tariff);

                new Chart(document.getElementById('energyChart'), {
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
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Power (kW)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });

                new Chart(document.getElementById('batteryChart'), {
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
                                    text: 'Battery Power (W)'
                                },
                                ticks: {
                                    color: 'blue'
                                },
                                grid: {
                                    drawTicks: true,
                                    color: '#eee'
                                }
                            },
                            yTariff: {
                                type: 'linear',
                                position: 'right',
                                suggestedMin: -0.25,
                                suggestedMax: 0.25,
                                title: {
                                    display: true,
                                    text: 'Energy Tariffs (€ / kWh)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    color: 'green'
                                }
                            }
                        }
                    }
                });
            });

        fetch("{{ asset('battery_savings.json') }}")
            .then(res => res.json())
            .then(data => {
                const entries = Object.entries(data).sort(([a], [b]) => new Date(a) - new Date(b));
                const labels = entries.map(([ts]) => new Date(ts).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                const savings = entries.map(([, val]) => val.battery_savings);
                const colors = savings.map(val => val >= 0 ? 'rgba(34,197,94,0.7)' : 'rgba(239,68,68,0.7)');
                const total = savings.reduce((sum, val) => sum + val, 0);
                document.getElementById('batteryEarningDisplay').innerText = `Total Earnings: €${total.toFixed(2)}`;

                new Chart(document.getElementById('batterySavingsChart'), {
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
                                    text: 'Savings (€)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
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






</x-app-layout>
