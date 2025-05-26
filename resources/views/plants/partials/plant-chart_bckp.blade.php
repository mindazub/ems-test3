<div class="mb-6 space-y-8">
    <!-- Energy Chart Tabs -->
    <div x-data="{ tabEnergy: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tabEnergy === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabEnergy = 'graph'">
                    Graph
                </button>
                <button
                    :class="tabEnergy === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabEnergy = 'data'">
                    Data
                </button>
            </nav>
            <!-- Dropdown, aligned right -->
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'energy', 'png']) }}">Download PNG</a>
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'energy', 'csv']) }}">Download CSV</a>
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'energy', 'pdf']) }}">Download PDF</a>
                </div>
            </div>
        </div>

        <div class="px-4 py-4 min-h-[550px]">
            <!-- Graph Tab -->
            <div x-show="tabEnergy === 'graph'">
                <h4 class="text-center mb-3 font-bold text-3xl">Energy Live Chart</h4>
                <div class="w-full" style="height: 600px;">
                    <canvas id="energyChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabEnergy === 'data'">
                <div class="overflow-x-auto" style="height: 650px;">
                    <table class="w-full text-lg border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-center">Time</th>
                            <th class="px-4 py-2 text-center">PV (kW)</th>
                            <th class="px-4 py-2 text-center">Battery (kW)</th>
                            <th class="px-4 py-2 text-center">Grid (kW)</th>
                        </tr>
                        </thead>
                        <tbody id="energyDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Battery Chart Tabs -->
    <div x-data="{ tabBattery: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tabBattery === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabBattery = 'graph'">
                    Graph
                </button>
                <button
                    :class="tabBattery === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabBattery = 'data'">
                    Data
                </button>
            </nav>
            <!-- Dropdown -->
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'battery', 'png']) }}">Download PNG</a>
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'battery', 'csv']) }}">Download CSV</a>
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'battery', 'pdf']) }}">Download PDF</a>
                </div>
            </div>
        </div>

        <div class="px-4 py-4 min-h-[550px]">
            <!-- Battery Chart Tab -->
            <div x-show="tabBattery === 'graph'">
                <h4 class="text-center mb-3 font-bold text-3xl">Battery Power and Energy Price</h4>
                <div class="w-full" style="height: 600px;">
                    <canvas id="batteryChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabBattery === 'data'">
                <div class="overflow-x-auto" style="height: 650px;">
                    <table class="w-full text-lg border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-center">Time</th>
                            <th class="px-4 py-2 text-center">Battery Power (kW)</th>
                            <th class="px-4 py-2 text-center">Energy Price (€ / kWh)</th>
                        </tr>
                        </thead>
                        <tbody id="batteryDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Battery Savings Chart Tabs -->
    <div x-data="{ tabSavings: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tabSavings === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabSavings = 'graph'">
                    Graph
                </button>
                <button
                    :class="tabSavings === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabSavings = 'data'">
                    Data
                </button>
            </nav>
            <!-- Dropdown -->
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'savings', 'png']) }}">Download PNG</a>
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'savings', 'csv']) }}">Download CSV</a>
                    <a class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->id, 'savings', 'pdf']) }}">Download PDF</a>
                </div>
            </div>
        </div>
        <div class="px-4 py-4 min-h-[550px]">
            <!-- Graph Tab -->
            <div x-show="tabSavings === 'graph'">
                <h4 class="text-center mb-3 font-bold text-3xl">Battery Savings</h4>
                <p id="batterySavingsTotal" class="text-center text-green-700 font-bold"></p>
                <div class="w-full" style="height: 600px;">
                    <canvas id="savingsChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabSavings === 'data'">
                <div class="overflow-x-auto" style="height: 670px;">
                    <table class="w-full text-lg border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-center">Time</th>
                            <th class="px-4 py-2 text-center">Battery Savings (€)</th>
                        </tr>
                        </thead>
                        <tbody id="batterySavingsDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Chart.js loader -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Alpine.js loader (required for x-data, if not already loaded elsewhere) -->
<script src="//unpkg.com/alpinejs" defer></script>

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
        // ENERGY CHART
        const entries = Object.entries(batteryOkData).sort(([a], [b]) => new Date(a) - new Date(b));
        const labels = entries.map(([ts]) => formatLabelDate(ts));
        const pvData = entries.map(([, v]) => v.pv_p / 1000);
        const batteryData = entries.map(([, v]) => v.battery_p / 1000);
        const gridData = entries.map(([, v]) => v.grid_p / 1000);

        // Energy Chart
        new Chart(document.getElementById('energyChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'PV (kW)', data: pvData,
                        borderColor: 'rgba(0,123,255,1)', backgroundColor: 'rgba(0,123,255,0.15)', fill: true, pointRadius: 2
                    },
                    {
                        label: 'Battery (kW)', data: batteryData,
                        borderColor: 'rgba(220,53,69,1)', backgroundColor: 'rgba(220,53,69,0.12)', fill: true, pointRadius: 2
                    },
                    {
                        label: 'Grid (kW)', data: gridData,
                        borderColor: 'rgba(40,167,69,1)', backgroundColor: 'rgba(40,167,69,0.12)', fill: true, pointRadius: 2
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                elements: { point: { radius: 4, hoverRadius: 8 } },
                scales: {
                    y: { ticks: { font: { size: 14 } } },
                    x: { ticks: { font: { size: 14 } } }
                }
            }

        });

        // Fill Energy Data Table
        const energyTable = document.getElementById('energyDataTableBody');
        energyTable.innerHTML = '';
        entries.forEach(([ts, val]) => {
            energyTable.innerHTML += `
            <tr>
                <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                <td class="px-4 py-2 text-center">${(val.pv_p / 1000).toFixed(2)}</td>
                <td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td>
                <td class="px-4 py-2 text-center">${(val.grid_p / 1000).toFixed(2)}</td>
            </tr>`;
        });

        // BATTERY CHART
        const tariffData = entries.map(([, v]) => v.tariff);

        new Chart(document.getElementById('batteryChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Battery Power (kW)',
                        data: batteryData,
                        borderColor: 'rgba(0,123,255,0.8)',
                        backgroundColor: 'rgba(0,123,255,0.15)',
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'Energy Price (€ / kWh)',
                        data: tariffData,
                        backgroundColor: 'rgba(40,167,69,0.5)',
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    // Add crosshair plugin config here if using
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                elements: {
                    point: {
                        radius: 4,        // normal
                        hoverRadius: 12   // << bigger on hover!
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        ticks: { font: { size: 16 } }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { font: { size: 14 } },
                        min: -0.25,
                        max: 0.25
                    },
                    x: {
                        ticks: { font: { size: 14 } },
                        border: { display: true, width: 4 }
                    }
                }
            }
        });



        // Fill Battery Data Table
        const batteryTable = document.getElementById('batteryDataTableBody');
        batteryTable.innerHTML = '';
        entries.forEach(([ts, val]) => {
            batteryTable.innerHTML += `
            <tr>
                <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                <td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td>
                <td class="px-4 py-2 text-center">${val.tariff.toFixed(4)}</td>
            </tr>`;
        });

        // BATTERY SAVINGS CHART
        const savingsEntries = Object.entries(batterySavingsData).sort(([a], [b]) => new Date(a) - new Date(b));
        const savingsLabels = savingsEntries.map(([ts]) => formatLabelDate(ts));
        const savingsData = savingsEntries.map(([, v]) => v.battery_savings);
        const totalSavings = savingsData.reduce((acc, val) => acc + val, 0);
        document.getElementById('batterySavingsTotal').textContent = `Your savings today: € ${totalSavings.toFixed(2)}`;

        new Chart(document.getElementById('savingsChart'), {
            type: 'bar',
            data: {
                labels: savingsLabels,
                datasets: [{
                    label: 'Battery Savings (€)',
                    data: savingsData,
                    backgroundColor: savingsData.map(val => val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)')
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { type: 'linear', position: 'left', ticks: { font: { size: 16 } } },
                    y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, ticks: { font: { size: 14 } } },
                    x: { ticks: { font: { size: 14 } } }
                }
            }
        });

        // Fill Savings Data Table
        const savingsTable = document.getElementById('batterySavingsDataTableBody');
        savingsTable.innerHTML = '';
        savingsEntries.forEach(([ts, val]) => {
            savingsTable.innerHTML += `
            <tr>
                <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                <td class="px-4 py-2 text-center">${val.battery_savings.toFixed(2)}</td>
            </tr>`;
        });
    }
</script>
