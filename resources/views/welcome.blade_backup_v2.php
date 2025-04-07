<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>EDISLAB | EMS Dashboard App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/edislab-high-resolution-logo-transparent.png') }}" alt="EDISLAB Logo"
                            class="h-10 w-auto">
                    </a>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Login</a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 rounded border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="text-center py-20 bg-white shadow-inner">
        <h1 class="text-4xl font-extrabold text-blue-800 mb-4">Welcome to Your EMS Dashboard!</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Monitor, analyze, and optimize your renewable energy systems with real-time insights and clean dashboards.
        </p>
    </section>

    <!-- ENERGY CHART -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="max-w-5xl mx-auto bg-white rounded shadow p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Live Energy Data from JSON</h2>
            <canvas id="energyChart" height="100"></canvas>
        </div>
    </section>

    <!-- BATTERY/TARIFF CHART -->
    <section class="py-16 px-4 bg-white">
        <div class="max-w-5xl mx-auto bg-white rounded shadow p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Battery Power and Energy Tariffs Over Time</h2>
            <canvas id="batteryChart" height="100"></canvas>
        </div>
    </section>

    <!-- BATTERY SAVINGS CHART -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="max-w-5xl mx-auto bg-white rounded shadow p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Battery Savings Over Time</h2>
            <canvas id="batterySavingsChart" height="100"></canvas>
        </div>
    </section>

    <script>
        // Fetch data from batteries_ok.json
        fetch("{{ asset('batteries_ok.json') }}")
            .then(response => response.json())
            .then(data => {
                // Convert object with timestamp keys into array of sorted entries
                const entries = Object.entries(data).sort(([a], [b]) => new Date(a) - new Date(b));

                const labels = entries.map(([ts]) => {
                    const date = new Date(ts);
                    return date.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                });

                const pvData = entries.map(([, val]) => val.pv_p);
                const batteryData = entries.map(([, val]) => val.battery_p);
                const gridData = entries.map(([, val]) => val.grid_p);
                const tariffData = entries.map(([, val]) => val.tariff);

                // ENERGY CHART
                const ctx = document.getElementById('energyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                                label: 'PV Production (kW)',
                                data: pvData,
                                borderColor: 'rgba(59, 130, 246, 1)',
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointRadius: 0
                            },
                            {
                                label: 'Grid Power (kW)',
                                data: gridData,
                                borderColor: 'rgba(34, 197, 94, 1)',
                                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointRadius: 0
                            },
                            {
                                label: 'Battery Power (kW)',
                                data: batteryData,
                                borderColor: 'rgba(249, 115, 22, 1)',
                                backgroundColor: 'rgba(249, 115, 22, 0.2)',
                                borderWidth: 2,
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
                                title: {
                                    display: true,
                                    text: 'Power (kW)'
                                },
                                beginAtZero: true
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

                // BATTERY/TARIFF CHART
                const ctx2 = document.getElementById('batteryChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                                label: 'Battery Power (W)',
                                data: batteryData,
                                borderColor: 'blue',
                                backgroundColor: 'rgba(0, 0, 255, 0.1)',
                                yAxisID: 'yBattery',
                                tension: 0.2,
                                pointRadius: 0
                            },
                            {
                                label: 'Energy Tariffs (€ / kWh)',
                                data: tariffData,
                                borderColor: 'green',
                                backgroundColor: 'rgba(0, 128, 0, 0.1)',
                                yAxisID: 'yTariff',
                                tension: 0.2,
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
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            yBattery: {
                                type: 'linear',
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Battery Power (W)'
                                },
                                ticks: {
                                    color: 'blue'
                                }
                            },
                            yTariff: {
                                type: 'linear',
                                position: 'right',
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
            })
            .catch(error => console.error("Error loading JSON charts:", error));
    </script>


</body>

</html>
