<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>EDISLAB | EMS Dashboard App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes flashColor {
            0% {
                color: red;
            }

            33% {
                color: green;
            }

            66% {
                color: blue;
            }

            100% {
                color: red;
            }
        }

        .animate-flash {
            animation: flashColor 1.5s infinite;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/edislab_monitoring.png') }}" alt="EDISLAB Logo" class="h-8 w-auto">
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
            <h2 class="text-2xl font-bold mb-2 text-center">Battery Savings Over Time</h2>
            <p id="batteryEarningDisplay"
                class="text-2xl font-bold mb-4 animate-flash border-4 border-gray-400 rounded-lg px-4 py-2 bg-yellow-200 shadow-md w-fit mx-auto text-center">
                Total Earnings: calculating...
            </p>
            <canvas id="batterySavingsChart" height="100"></canvas>
        </div>
    </section>

    <script>
        // Load first two charts from batteries_ok.json
        fetch("{{ asset('batteries_ok.json') }}")
            .then(response => response.json())
            .then(data => {
                const entries = Object.entries(data).sort(([a], [b]) => Number(a) - Number(b));
                const labels = entries.map(([ts]) => {
                    const date = new Date(isNaN(ts) ? ts : Number(ts));
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
                new Chart(document.getElementById('energyChart').getContext('2d'), {
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
                new Chart(document.getElementById('batteryChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                                type: 'line', // Battery line
                                label: 'Battery Power (W)',
                                data: batteryData,
                                borderColor: 'blue',
                                backgroundColor: 'rgba(0, 0, 255, 0.1)',
                                yAxisID: 'yBattery',
                                tension: 0.2,
                                pointRadius: 0
                            },
                            {
                                type: 'bar', // Tariff bar
                                label: 'Energy Tariffs (€ / kWh)',
                                data: tariffData,
                                backgroundColor: 'rgba(0, 128, 0, 0.5)',
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
                                suggestedMin: -15000, // ⬅️ Your custom min
                                suggestedMax: 15000, // ⬅️ Your custom max
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
                                suggestedMin: -0.25, // ⬅️ Your custom min
                                suggestedMax: 0.25, // ⬅️ Your custom max
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

        // Load battery savings chart from battery_savings.json
        fetch("{{ asset('battery_savings.json') }}")
            .then(response => response.json())
            .then(data => {
                const entries = Object.entries(data).sort(([a], [b]) => new Date(a) - new Date(b));
                const labels = entries.map(([ts]) => {
                    const date = new Date(ts);
                    return date.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                });

                const batterySavingsData = entries.map(([, val]) => val.battery_savings);
                const batterySavingsColors = batterySavingsData.map(val =>
                    val >= 0 ? 'rgba(34, 197, 94, 0.7)' : 'rgba(239, 68, 68, 0.7)'
                );

                const totalEarnings = batterySavingsData.reduce((sum, val) => sum + val, 0);
                document.getElementById('batteryEarningDisplay').innerText =
                    `Total Earnings: €${totalEarnings.toFixed(2)}`;

                new Chart(document.getElementById('batterySavingsChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Battery Savings (€)',
                            data: batterySavingsData,
                            backgroundColor: batterySavingsColors,
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
                                        return `${context.dataset.label}: €${context.parsed.y.toFixed(2)}`;
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

</body>

</html>
