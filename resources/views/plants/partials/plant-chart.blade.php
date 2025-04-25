<!-- resources/views/plants/partials/plant-chart.blade.php -->
<div class="mb-6">
    <!-- Chart Tabs and Containers will be populated here -->
    <div id="chart-sections">
        <div class="alert alert-info">Charts loading...</div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", async function() {
        const container = document.getElementById("chart-sections");
        try {
            const [batteryOkRes, batterySavingsRes] = await Promise.all([
                fetch("{{ asset('batteries_ok.json') }}").then(res => res.json()),
                fetch("{{ asset('battery_savings.json') }}").then(res => res.json()),
            ]);

            // You can now use these data variables to render your chart canvas blocks + populate tables
            container.innerHTML = `
                <div class="card mb-4">
                    <div class="card-header">Energy Live Chart</div>
                    <div class="card-body">
                        <canvas id="energyChart" height="200"></canvas>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-sm" id="energyDataTable">
                                <thead><tr><th>Time</th><th>PV</th><th>Battery</th><th>Grid</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">Battery Tariff</div>
                    <div class="card-body">
                        <canvas id="batteryChart" height="200"></canvas>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-sm" id="batteryDataTable">
                                <thead><tr><th>Time</th><th>Battery</th><th>Tariff</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">Battery Savings</div>
                    <div class="card-body">
                        <canvas id="batterySavingsChart" height="200"></canvas>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-sm" id="batterySavingsDataTable">
                                <thead><tr><th>Time</th><th>Battery Savings (€)</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            renderCharts(batteryOkRes, batterySavingsRes);
        } catch (e) {
            console.error("Chart rendering failed", e);
            container.innerHTML = `<div class="alert alert-danger">Failed to load chart data.</div>`;
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

        const energyCtx = document.getElementById('energyChart').getContext('2d');
        new Chart(energyCtx, {
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

        // Populate table
        const tableBody = document.querySelector('#energyDataTable tbody');
        entries.forEach(([ts, val]) => {
            tableBody.innerHTML +=
                `<tr><td>${new Date(Number(ts)).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td><td>${val.pv_p.toFixed(2)}</td><td>${val.battery_p.toFixed(2)}</td><td>${val.grid_p.toFixed(2)}</td></tr>`;
        });

        // Battery Chart
        const batteryCtx = document.getElementById('batteryChart').getContext('2d');
        new Chart(batteryCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                        label: 'Battery Power',
                        data: batteryData,
                        backgroundColor: 'rgba(0, 123, 255, 0.5)'
                    },
                    {
                        label: 'Tariff',
                        data: tariffData,
                        backgroundColor: 'rgba(40, 167, 69, 0.5)'
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

        const batteryTable = document.querySelector('#batteryDataTable tbody');
        entries.forEach(([ts, val]) => {
            batteryTable.innerHTML +=
                `<tr><td>${new Date(Number(ts)).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td><td>${val.battery_p.toFixed(2)}</td><td>${val.tariff.toFixed(4)}</td></tr>`;
        });

        // Battery Savings Chart
        const savingsEntries = Object.entries(batterySavingsData).sort(([a], [b]) => new Date(a) - new Date(b));
        const savingsLabels = savingsEntries.map(([ts]) => new Date(ts).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        }));
        const savingsData = savingsEntries.map(([, v]) => v.battery_savings);
        const savingsColors = savingsData.map(val => val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)');

        const savingsCtx = document.getElementById('batterySavingsChart').getContext('2d');
        new Chart(savingsCtx, {
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
