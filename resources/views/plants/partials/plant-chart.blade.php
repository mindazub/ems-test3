<div class="mb-6">
    <!-- Chart Tabs and Containers -->
    <div class="card mb-5">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs" id="energyTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="graph-tab" data-bs-toggle="tab" data-bs-target="#graphTab"
                        type="button" role="tab" aria-controls="graphTab" aria-selected="true">Graph</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#dataTab"
                        type="button" role="tab" aria-controls="dataTab" aria-selected="false">Data</button>
                </li>
                <li class="nav-item ms-auto" role="presentation">
                    <div class="nav-link p-0 border-0 bg-transparent">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="energyDownloadMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="energyDownloadMenu">
                                <li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'energy', 'png']) }}">Download PNG</a></li>
                                <li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'energy', 'csv']) }}">Download CSV</a></li>
                                <li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'energy', 'pdf']) }}">Download PDF</a></li>
                                                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="card-body tab-content" id="energyTabContent" style="height: 550px;">
            <!-- Graph Tab -->
            <div class="tab-pane fade show active h-100" id="graphTab" role="tabpanel" aria-labelledby="graph-tab">
                <h4 class="text-center m-3">Energy Live Chart</h4>
                <div style="height: calc(100% - 90px); display: flex; align-items: center; width: 100%;">
                    <canvas id="energyChart" style="width: 100%; height: 400px;"></canvas>
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

    <!-- Battery Chart Tabs and Containers -->
    <div class="card mb-5">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs" id="batteryTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="battery-graph-tab" data-bs-toggle="tab"
                        data-bs-target="#batteryGraphTab" type="button" role="tab" aria-controls="batteryGraphTab"
                        aria-selected="true">Graph</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="battery-data-tab" data-bs-toggle="tab" data-bs-target="#batteryDataTab"
                        type="button" role="tab" aria-controls="batteryDataTab" aria-selected="false">Data</button>
                </li>
                <li class="nav-item ms-auto" role="presentation">
                    <div class="nav-link p-0 border-0 bg-transparent">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="batteryDownloadMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i>
                            </button>
{{-- BATTERY Chart Buttons --}}
<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="batteryPowerDownloadMenu">
<li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'battery', 'png']) }}">Download PNG</a></li>
<li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'battery', 'csv']) }}">Download CSV</a></li>
<li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'battery', 'pdf']) }}">Download PDF</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content" id="batteryTabContent" style="height: 550px;">
            <div class="tab-pane fade show active h-100" id="batteryGraphTab" role="tabpanel"
                aria-labelledby="battery-graph-tab">
                <h4 class="text-center m-3">Battery Power and Energy Price</h4>
                <div style="height: calc(100% - 90px); display: flex; align-items: center;">
                    <canvas id="batteryChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            <div class="tab-pane fade h-100" id="batteryDataTab" role="tabpanel" aria-labelledby="battery-data-tab">
                <div class="table-responsive h-100">
                    <table id="batteryDataTable" class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Battery Power (kW)</th>
                                <th>Energy Price (€ / kWh)</th>
                            </tr>
                        </thead>
                        <tbody id="batteryDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Battery Savings Chart Tabs and Containers -->
<div class="card mb-5">
    <div class="card-header pb-0">
        <ul class="nav nav-tabs" id="savingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="savings-graph-tab" data-bs-toggle="tab"
                    data-bs-target="#savingsGraphTab" type="button" role="tab" aria-controls="savingsGraphTab"
                    aria-selected="true">Graph</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="savings-data-tab" data-bs-toggle="tab" data-bs-target="#savingsDataTab"
                    type="button" role="tab" aria-controls="savingsDataTab" aria-selected="false">Data</button>
            </li>
            <li class="nav-item ms-auto" role="presentation">
                <div class="nav-link p-0 border-0 bg-transparent">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                            id="savingsDownloadMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="savingsDownloadMenu">
{{-- SAVINGS Chart Buttons --}}

    <li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'savings', 'png']) }}">Download PNG</a></li>
    <li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'savings', 'csv']) }}">Download CSV</a></li>
    <li><a class="dropdown-item" href="{{ route('plants.download', [$plant->id, 'savings', 'pdf']) }}">Download PDF</a></li>
                                </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content" id="savingsTabContent" style="height: 550px;">
        <div class="tab-pane fade show active h-100" id="savingsGraphTab" role="tabpanel"
            aria-labelledby="savings-graph-tab">
            <h4 class="text-center m-3">Battery Savings</h4>
            <p id="batterySavingsTotal" class="text-center text-success fw-semibold"></p>
            <div style="height: calc(100% - 90px); display: flex; align-items: center;">
                <canvas id="savingsChart" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>
        <div class="tab-pane fade h-100" id="savingsDataTab" role="tabpanel" aria-labelledby="savings-data-tab">
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

<!-- Load Chart.js FIRST -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

// Crosshair plugin for Chart.js
const verticalLinePlugin = {
    id: 'verticalLine',
    afterDraw(chart) {
        // Only draw if tooltip is active and chartArea is defined
        if (chart.tooltip?._active?.length && chart.chartArea) {
            const x = chart.tooltip._active[0].element.x;
            const ctx = chart.ctx;
            const topY = chart.chartArea.top;
            const bottomY = chart.chartArea.bottom;

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x, topY);
            ctx.lineTo(x, bottomY);
            ctx.lineWidth = 3; // Thicker line for visibility
            ctx.strokeStyle = 'rgba(255,0,0,0.9)';
            ctx.stroke();
            ctx.restore();
        }
    }
};

Chart.register(verticalLinePlugin);

</script>

<script>

    document.addEventListener("DOMContentLoaded", async function () {


        try {
            const [batteryOkRes, batterySavingsRes] = await Promise.all([
                fetch("{{ asset('energy_live_chart.json') }}").then(res => res.json()),
                fetch("{{ asset('battery_savings.json') }}").then(res => res.json()),
            ]);
            renderCharts(batteryOkRes, batterySavingsRes);
        } catch (e) {
            console.error("Chart rendering failed", e);
        }
    });

    function formatLabelDate(ts) {
        const date = isNaN(ts) ? new Date(ts) : new Date(Number(ts));
        return date.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });
    }




    function renderCharts(batteryOkData, batterySavingsData) {
        const entries = Object.entries(batteryOkData).sort(([a], [b]) => new Date(a) - new Date(b));
        const labels = entries.map(([ts]) => formatLabelDate(ts));

        const pvData = entries.map(([, v]) => v.pv_p);
        const batteryData = entries.map(([, v]) => v.battery_p);
        const gridData = entries.map(([, v]) => v.grid_p);
        const tariffData = entries.map(([, v]) => v.tariff);




        // --- Energy Chart ---
        const pvDataKW = pvData.map(v => v / 1000);
        const batteryDataKW00 = batteryData.map(v => v / 1000);
        const gridDataKW = gridData.map(v => v / 1000);


        window.energyChart = new Chart(document.getElementById('energyChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'PV (kW)',
                        data: pvDataKW,
                        borderColor: 'blue',
                        fill: false,
                        pointRadius: 2,        // <-- Make points bigger
                        pointHoverRadius: 12   // <-- Make points bigger on hover
                    },
                    {
                        label: 'Battery (kW)',
                        data: batteryDataKW00,
                        borderColor: 'orange',
                        fill: false,
                        pointRadius: 2,
                        pointHoverRadius: 12
                    },
                    {
                        label: 'Grid (kW)',
                        data: gridDataKW,
                        borderColor: 'green',
                        fill: false,
                        pointRadius: 2,
                        pointHoverRadius: 12
                    },
                ]
            },
            options: {
                responsive: true, // <-- set to true
                maintainAspectRatio: false, // <-- add this for full height
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                hover: {
                    mode: 'index',
                    intersect: false
                },
                elements: {
                    point: {
                        radius: 4,         // Default point size
                        hoverRadius: 8    // Point size on hover
                    }
                },
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Power (kW)'
                        },

                        grid: {
                            lineWidth: 1,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        }
                    },
                    x: {

                        ticks: {
                            font: { weight: 'normal' }
                        },
                    }
                }
            }
        });


        const energyTable = document.querySelector('#energyDataTableBody');
        entries.forEach(([ts, val]) => {
            energyTable.innerHTML += `
                <tr>
                    <td>${formatLabelDate(ts)}</td>
                    <td>${val.pv_p.toFixed(2)}</td>
                    <td>${val.battery_p.toFixed(2)}</td>
                    <td>${val.grid_p.toFixed(2)}</td>
                </tr>`;
        });

        setTimeout(() => uploadChartImage('energyChart', window.energyChart, {{ $plant->id }}), 800);

        // --- Battery Chart ---
        const batteryDataKW = batteryData.map(v => v / 1000);

        window.batteryChart = new Chart(document.getElementById('batteryChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        type: 'line', // ✅ Change this!
                        label: 'Battery Power (kW)',
                        data: batteryDataKW,
                        borderColor: 'rgba(0,123,255,0.8)',
                        backgroundColor: 'rgba(0,123,255,0.3)',
                        fill: false,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar', // ✅ Keep bar
                        label: 'Energy Price (€ / kWh)',
                        data: tariffData,
                        backgroundColor: 'rgba(40,167,69,0.5)',
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        min: -30,
                        max: 30,
                        ticks: {
                            callback: v => v.toLocaleString(undefined, { maximumFractionDigits: 2 }),
                            font: { weight: 'bold' }
                        },
                        title: {
                            display: true,
                            text: 'Battery Power (kW)'
                        },
                        grid: {
                            lineWidth: 1,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        min: -0.25,
                        max: 0.25,
                        grid: { drawOnChartArea: false },
                        title: {
                            display: true,
                            text: 'Energy Price (€ / kWh)'
                        },
                        ticks: {
                            font: { weight: 'bold' }
                        },
                        grid: {
                            lineWidth: 1,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        }
                    },
                    x: {
                        ticks: {
                            font: { weight: 'bold' }
                        },
                        grid: {
                            lineWidth: context => context.tick && context.tick.value === 0 ? 2 : 0.5,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        }
                    }
                }
            }
        });

        const batteryTable = document.querySelector('#batteryDataTableBody');
        entries.forEach(([ts, val]) => {
            batteryTable.innerHTML += `
                <tr>
                    <td>${formatLabelDate(ts)}</td>
                    <td>${val.battery_p.toFixed(2)}</td>
                    <td>${val.tariff.toFixed(4)}</td>
                </tr>`;
        });

        setTimeout(() => uploadChartImage('batteryChart', window.batteryChart, {{ $plant->id }}), 800);

        // --- Battery Savings Chart ---
        const savingsEntries = Object.entries(batterySavingsData).sort(([a, b]) => new Date(a) - new Date(b));
        const savingsLabels = savingsEntries.map(([ts]) => formatLabelDate(ts));
        const savingsData = savingsEntries.map(([, v]) => v.battery_savings);
        const savingsColors = savingsData.map(val => val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)');
        const totalSavings = savingsData.reduce((acc, val) => acc + val, 0);
        document.getElementById('batterySavingsTotal').textContent = `Your savings today: € ${totalSavings.toFixed(2)}`;

        window.batterySavingsChart = new Chart(document.getElementById('savingsChart'), {
            type: 'bar',
            data: {
                labels: savingsLabels,
                datasets: [{
                    label: 'Battery Savings (€)',
                    data: savingsData,
                    backgroundColor: savingsColors
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            font: { weight: 'bold' }
                        },
                        grid: {
                            lineWidth: 1,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        },
                        title: {
                            display: true,
                            text: 'Battery Savings (€)'
                        }
                    },
                    x: {
                        ticks: {
                            font: { weight: 'bold' }
                        },
                        grid: {
                            lineWidth: context => context.tick && context.tick.value === 0 ? 2 : 0.5,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        }
                    }
                }
            }
        });

        const savingsTable = document.querySelector('#batterySavingsDataTableBody');
        savingsEntries.forEach(([ts, val]) => {
            savingsTable.innerHTML += `
                <tr>
                    <td>${formatLabelDate(ts)}</td>
                    <td>${val.battery_savings.toFixed(2)}</td>
                </tr>`;
        });

        setTimeout(() => uploadChartImage('savingsChart', window.batterySavingsChart, {{ $plant->id }}), 800);
    }

    function uploadChartImage(chartId, chartInstance, plantId) {
        const canvas = document.getElementById(chartId);
        const tempCanvas = document.createElement("canvas");
        tempCanvas.width = canvas.width;
        tempCanvas.height = canvas.height;

        const ctx = tempCanvas.getContext("2d");
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        ctx.drawImage(canvas, 0, 0);

        const imageData = tempCanvas.toDataURL("image/png");

        fetch("{{ route('charts.upload') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({
                plant_id: plantId,
                chart: chartId.replace('Chart', '').toLowerCase(),
                image: imageData
            })
        })
            .then(res => res.json())
            .then(res => console.log("✅ Uploaded high-res:", chartId))
            .catch(err => console.error("❌ Upload failed:", err));
    }



    </script>


