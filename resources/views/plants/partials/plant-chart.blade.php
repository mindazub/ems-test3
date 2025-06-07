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
            <div class="ml-auto relative" x-data="{ openMenu: false }" data-plant-uid="{{ $plant->uid }}">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-energy" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer" href="#">Download PNG</a>
                     <a id="downloadCSV-energy" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'energy', 'csv']) }}">Download CSV</a>
                     <a id="downloadPDF-energy" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer" href="#">Download PDF</a>

                </div>
            </div>
        </div>

        <div class="px-4 py-4 min-h-[550px]">
            <!-- Graph Tab -->
            <div x-show="tabEnergy === 'graph'">
                <h4 class="text-center mb-5 font-bold text-3xl">Energy Live Chart</h4>
                <div class="w-full" style="height: 600px;">
                    <canvas id="energyChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabEnergy === 'data'">
                <div class="overflow-x-auto" style="height: 650px;">
                    <table class="w-full text-sm border rounded">
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
            <div class="ml-auto relative" x-data="{ openMenu: false }" data-plant-uid="{{ $plant->uid }}">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-battery" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer" href="#">Download PNG</a>
                     <a id="downloadCSV-battery" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'battery', 'csv']) }}">Download CSV</a>
                     <a id="downloadPDF-battery" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer" href="#">Download PDF</a>
                </div>
            </div>
        </div>

        <div class="px-4 py-4 min-h-[550px]">
            <!-- Battery Chart Tab -->
            <div x-show="tabBattery === 'graph'">
                <h4 class="text-center mb-5 font-bold text-3xl">Battery Power and Energy Price</h4>
                <div class="w-full" style="height: 600px;">
                    <canvas id="batteryChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabBattery === 'data'">
                <div class="overflow-x-auto" style="height: 650px;">
                    <table class="w-full text-sm border rounded">
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
            <div class="ml-auto relative" x-data="{ openMenu: false }" data-plant-uid="{{ $plant->uid }}">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-savings" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer" href="#">Download PNG</a>
                     <a id="downloadCSV-savings" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'savings', 'csv']) }}">Download CSV</a>
                     <a id="downloadPDF-savings" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer" href="#">Download PDF</a>
                </div>
            </div>
        </div>
        <div class="px-4 py-4 min-h-[550px]">
            <!-- Graph Tab -->
            <div x-show="tabSavings === 'graph'">
                <h4 class="text-center mb-5 font-bold text-3xl">Battery Savings</h4>
                <p id="batterySavingsTotal" class="text-center text-green-700 font-bold"></p>
                <div class="w-full" style="height: 600px;">
                    <canvas id="savingsChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabSavings === 'data'">
                <div class="overflow-x-auto" style="height: 670px;">
                    <table class="w-full text-sm border rounded">
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
// Vertical crosshair line plugin for Chart.js (applies to all charts)
const verticalLinePlugin = {
    id: 'verticalLine',
    afterDraw(chart) {
        if (chart.tooltip?._active && chart.tooltip._active.length && chart.chartArea) {
            const ctx = chart.ctx;
            const x = chart.tooltip._active[0].element.x;
            const topY = chart.chartArea.top;
            const bottomY = chart.chartArea.bottom;
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x, topY);
            ctx.lineTo(x, bottomY);
            ctx.lineWidth = 3; // Make it bolder
            ctx.strokeStyle = 'rgba(90,90,220,0.4)';
            ctx.setLineDash([6, 6]);
            ctx.stroke();
            ctx.restore();
        }
    }
};
Chart.register(verticalLinePlugin);

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

// Get user time format preference from backend (default to 24)
const userTimeFormat = @json($user->settings['time_format'] ?? '24');

function formatLabelDate(ts) {
    const date = isNaN(ts) ? new Date(ts) : new Date(Number(ts));
    if (userTimeFormat === '12') {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    } else {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    }
}

function renderCharts(batteryOkData, batterySavingsData) {
    // ENERGY CHART
    const entries = Object.entries(batteryOkData).sort(([a], [b]) => new Date(a) - new Date(b));
    const labels = entries.map(([ts]) => formatLabelDate(ts));
    const pvData = entries.map(([, v]) => v.pv_p / 1000);
    const batteryData = entries.map(([, v]) => v.battery_p / 1000);
    const gridData = entries.map(([, v]) => v.grid_p / 1000);

    new Chart(document.getElementById('energyChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'PV (kW)', data: pvData,
                    borderColor: 'rgba(0,123,255,1)', backgroundColor: 'rgba(0,123,255,0.15)', fill: true, pointRadius: 2, pointHoverRadius: 12
                },
                {
                    label: 'Battery (kW)', data: batteryData,
                    borderColor: 'rgba(220,53,69,1)', backgroundColor: 'rgba(220,53,69,0.12)', fill: true, pointRadius: 2, pointHoverRadius: 12
                },
                {
                    label: 'Grid (kW)', data: gridData,
                    borderColor: 'rgba(40,167,69,1)', backgroundColor: 'rgba(40,167,69,0.12)', fill: true, pointRadius: 2, pointHoverRadius: 12
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', padding: 30 } },
            interaction: { mode: 'index', intersect: false },
            elements: { point: { radius: 4, hoverRadius: 12 } },
            scales: {

                y: {
                    title: {
                            display: true,
                            text: 'Power (kW)'
                    },
                    ticks:
                        { font:
                            {
                                size: 14
                            }
                     },
                     grid: {
                            lineWidth: 1,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        }
                    },
                x: { ticks: { font: { size: 14 } }, border: { display: true, width: 4 } }
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
                    // hoverBackgroundColor: 'rgba(0,123,255,1)',
                    fill: true,
                    yAxisID: 'y',
                    pointRadius: 4, pointHoverRadius: 12
                },
                {
                    type: 'bar',
                    label: 'Energy Price (€ / kWh)',
                    data: tariffData,
                    backgroundColor: 'rgba(40,167,69,0.5)',
                    hoverBackgroundColor: 'rgba(40,167,69,1)',

                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', padding: 30 },
                // crosshair plugin config
            },
            interaction: { mode: 'index', intersect: false },
            elements: { point: { radius: 4, hoverRadius: 12 } },
            scales: {
                y: {
                    title: {
                            display: true,
                            text: 'Battery Power (kW)'
                    },
                    type: 'linear',
                    position: 'left',
                    ticks: { font: { size: 14 } },
                    min: -30,
                    max: 30
                },
                y1: {
                    title: {
                            display: true,
                            text: 'Energy Price (€ / kWh)'
                    },
                    type: 'linear',
                    position: 'right',
                    grid: {
                            lineWidth: 1,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                    },
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
                backgroundColor: savingsData.map(val => val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)'),
                hoverBackgroundColor: savingsData.map(val => val >= 0 ? 'rgba(25,135,84,1)' : 'rgba(220,53,69,1)')

            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false} },
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { title: {
                            display: true,
                            text: 'Battery Savings (€)'
                    },
                type: 'linear', position: 'left', ticks: { font: { size: 14 } },
                grid: {
                                lineWidth: 1,
                                color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                            }, },

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


<script>
    /**
     * Send chart image to backend and initiate download
     * @param {string} chartId - DOM ID of the chart canvas element
     * @param {string} chartName - Type of chart (energy, battery, or savings)
     * @param {string} plantUid - Plant's unique identifier
     * @param {string} type - Download type (png, csv, pdf)
     */
    function sendChartToBackend(chartId, chartName, plantUid, type) {
        // Get the chart canvas element
        let canvas = document.getElementById(chartId);
        if (!canvas) {
            alert('Chart not rendered yet! Please wait for the chart to load.');
            return;
        }

        // Show loading indicator
        const loadingElement = document.createElement('div');
        loadingElement.className = 'loading-indicator';
        loadingElement.innerHTML = 'Preparing download, please wait...';
        loadingElement.style = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); color: white; padding: 15px; border-radius: 5px; z-index: 9999;';
        document.body.appendChild(loadingElement);

        // Convert canvas to PNG data URL
        let dataUrl = canvas.toDataURL('image/png');

        // Send image data to backend
        return fetch(`/plants/${plantUid}/save-chart-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                chart: chartName,
                image: dataUrl
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Remove loading indicator
            document.body.removeChild(loadingElement);

            // Redirect to download if successful
            if (data.success) {
                window.location.href = `/plants/${plantUid}/download/${chartName}/${type}`;
            } else {
                alert('Error saving chart image: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            // Remove loading indicator and show error
            document.body.removeChild(loadingElement);
            console.error('Chart download failed:', error);
            alert('Download failed: ' + error.message);
        });
    }

    /**
     * Download chart data as CSV
     * @param {string} chartName - Type of chart (energy, battery, or savings)
     * @param {string} plantUid - Plant's unique identifier
     */
    function downloadCSV(chartName, plantUid) {
        const loadingElement = document.createElement('div');
        loadingElement.className = 'loading-indicator';
        loadingElement.innerHTML = 'Preparing CSV download, please wait...';
        loadingElement.style = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); color: white; padding: 15px; border-radius: 5px; z-index: 9999;';
        document.body.appendChild(loadingElement);

        setTimeout(() => {
            document.body.removeChild(loadingElement);
            window.location.href = `/plants/${plantUid}/download/${chartName}/csv`;
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get plant UID from the dropdown container's data attribute for robustness
        let plantDropdown = document.querySelector('.ml-auto[data-plant-uid]');
        let plantUid = plantDropdown ? plantDropdown.getAttribute('data-plant-uid') : '';
        if (!plantUid) {
            console.warn('No plant uid found for chart download actions!');
            // Add error notice to the page
            const errorNotice = document.createElement('div');
            errorNotice.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
            errorNotice.innerHTML = '<strong class="font-bold">Warning!</strong><span class="block sm:inline"> Unable to identify plant for download actions. Download functionality may not work properly.</span>';
            document.querySelector('.mb-6').prepend(errorNotice);
        }
        // ENERGY
        document.getElementById('downloadPNG-energy').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) sendChartToBackend('energyChart', 'energy', plantUid, 'png');
        });
        document.getElementById('downloadPDF-energy').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) sendChartToBackend('energyChart', 'energy', plantUid, 'pdf');
        });
        document.getElementById('downloadCSV-energy').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) downloadCSV('energy', plantUid);
        });
        // BATTERY
        document.getElementById('downloadPNG-battery').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) sendChartToBackend('batteryChart', 'battery', plantUid, 'png');
        });
        document.getElementById('downloadPDF-battery').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) sendChartToBackend('batteryChart', 'battery', plantUid, 'pdf');
        });
        document.getElementById('downloadCSV-battery').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) downloadCSV('battery', plantUid);
        });
        // SAVINGS
        document.getElementById('downloadPNG-savings').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) sendChartToBackend('savingsChart', 'savings', plantUid, 'png');
        });
        document.getElementById('downloadPDF-savings').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) sendChartToBackend('savingsChart', 'savings', plantUid, 'pdf');
        });
        document.getElementById('downloadCSV-savings').addEventListener('click', function(e) {
            e.preventDefault();
            if (plantUid) downloadCSV('savings', plantUid);
        });
    });
</script>
