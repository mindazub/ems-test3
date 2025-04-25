<div class="mb-6">
    <!-- Chart Tabs and Containers -->
    <div class="card mb-4">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs" id="energyTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#energyGraph">Graph</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#energyData">Data</button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content">
            <div class="tab-pane fade show active" id="energyGraph">
                <canvas id="energyChart" height="200"></canvas>
            </div>
            <div class="tab-pane fade" id="energyData">
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-sm" id="energyDataTable">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>PV</th>
                                <th>Battery</th>
                                <th>Grid</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs" id="batteryTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#batteryGraph">Graph</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#batteryData">Data</button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content">
            <div class="tab-pane fade show active" id="batteryGraph">
                <canvas id="batteryChart" height="200"></canvas>
            </div>
            <div class="tab-pane fade" id="batteryData">
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-sm" id="batteryDataTable">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Battery</th>
                                <th>Tariff</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs" id="savingsTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#savingsGraph">Graph</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#savingsData">Data</button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content">
            <div class="tab-pane fade show active" id="savingsGraph">
                <canvas id="batterySavingsChart" height="200"></canvas>
            </div>
            <div class="tab-pane fade" id="savingsData">
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-sm" id="batterySavingsDataTable">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Battery Savings (€)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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

    function renderCharts(batteryOkData, batterySavingsData) {
        const entries = Object.entries(batteryOkData).sort(([a], [b]) => Number(a) - Number(b));
        const labels = entries.map(([ts]) => new Date(Number(ts)).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        }));
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
                `<tr><td>${new Date(Number(ts)).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td><td>${val.pv_p.toFixed(2)}</td><td>${val.battery_p.toFixed(2)}</td><td>${val.grid_p.toFixed(2)}</td></tr>`;
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
                `<tr><td>${new Date(Number(ts)).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td><td>${val.battery_p.toFixed(2)}</td><td>${val.tariff.toFixed(4)}</td></tr>`;
        });

        const savingsEntries = Object.entries(batterySavingsData).sort(([a], [b]) => new Date(a) - new Date(b));
        const savingsLabels = savingsEntries.map(([ts]) => new Date(ts).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        }));
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
                `<tr><td>${new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td><td>€${val.battery_savings.toFixed(2)}</td></tr>`;
        });
    }
</script>
