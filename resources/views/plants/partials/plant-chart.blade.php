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
            <div class="tab-pane fade show active h-100" id="graphTab" role="tabpanel" aria-labelledby="graph-tab">
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
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="batteryDownloadMenu">
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
            <div class="tab-pane fade show active h-100" id="batteryGraphTab" role="tabpanel"
                aria-labelledby="battery-graph-tab">
                <h4 class="text-center m-3">Battery Power and Tariff</h4>
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
                            <li><a class="dropdown-item" href="#"
                                    onclick="downloadChartImage('batterySavingsChart')">Download PNG</a></li>
                            <li><a class="dropdown-item" href="#"
                                    onclick="downloadChartCSV('batterySavingsChart', window.batterySavingsChart)">Download
                                    CSV</a></li>
                            <li><a class="dropdown-item" href="#"
                                    onclick="downloadChartPDF('batterySavingsChart', 'batterySavingsDataTable')">Download
                                    PDF</a></li>
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
            <div style="height: calc(100% - 90px); display: flex; align-items: center;">
                <canvas id="batterySavingsChart" style="width: 100%; height: 100%;"></canvas>
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


<script>
    document.addEventListener("DOMContentLoaded", async function() {
        try {
            const [batteryOkRes, batterySavingsRes] = await Promise.all([
                fetch("{{ asset('batteries_ok.json') }}").then(res => res.json()),
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

        new Chart(document.getElementById('energyChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                        label: 'PV',
                        data: pvData,
                        borderColor: 'blue',
                        fill: false
                    },
                    {
                        label: 'Battery',
                        data: batteryData,
                        borderColor: 'orange',
                        fill: false
                    },
                    {
                        label: 'Grid',
                        data: gridData,
                        borderColor: 'green',
                        fill: false
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        const energyTable = document.querySelector('#energyDataTable tbody');
        entries.forEach(([ts, val]) => {
            energyTable.innerHTML +=
                `<tr><td>${formatLabelDate(ts)}</td><td>${val.pv_p.toFixed(2)}</td><td>${val.battery_p.toFixed(2)}</td><td>${val.grid_p.toFixed(2)}</td></tr>`;
        });

        new Chart(document.getElementById('batteryChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                        label: 'Battery Power',
                        data: batteryData,
                        backgroundColor: 'rgba(0,123,255,0.5)'
                    },
                    {
                        label: 'Tariff',
                        data: tariffData,
                        backgroundColor: 'rgba(40,167,69,0.5)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        const batteryTable = document.querySelector('#batteryDataTable tbody');
        entries.forEach(([ts, val]) => {
            batteryTable.innerHTML +=
                `<tr><td>${formatLabelDate(ts)}</td><td>${val.battery_p.toFixed(2)}</td><td>${val.tariff.toFixed(4)}</td></tr>`;
        });

        const savingsEntries = Object.entries(batterySavingsData).sort(([a], [b]) => new Date(a) - new Date(b));
        const savingsLabels = savingsEntries.map(([ts]) => formatLabelDate(ts));
        const savingsData = savingsEntries.map(([, v]) => v.battery_savings);
        const savingsColors = savingsData.map(val => val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)');

        new Chart(document.getElementById('batterySavingsChart'), {
            type: 'bar',
            data: {
                labels: savingsLabels,
                datasets: [{
                    label: 'Battery Savings',
                    data: savingsData,
                    backgroundColor: savingsColors
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        const savingsTable = document.querySelector('#batterySavingsDataTable tbody');
        savingsEntries.forEach(([ts, val]) => {
            savingsTable.innerHTML +=
                `<tr><td>${formatLabelDate(ts)}</td><td>€${val.battery_savings.toFixed(2)}</td></tr>`;
        });
    }
</script>
