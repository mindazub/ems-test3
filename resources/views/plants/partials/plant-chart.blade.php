

<!-- Add calendar controls above each chart -->
<div class="flex items-center justify-center mb-2 gap-2" id="energy-calendar-controls">
                <button id="energy-prev" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300" title="Previous hour">&#8592;</button>
                <input type="date" id="energy-date" class="border rounded px-2 py-1" />
                <button id="energy-next" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300" title="Next hour">&#8594;</button>
            </div>



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
                     <a id="downloadPNG-energy" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PNG</a>
                     @if(!empty($plant->uid))
                         <a id="downloadCSV-energy" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'energy', 'csv']) }}">Download CSV</a>
                     @else
                         <span class="block px-4 py-2 text-gray-400 cursor-not-allowed" title="Plant ID missing">Download CSV</span>
                     @endif
                     <a id="downloadPDF-energy" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PDF</a>

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
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-battery" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PNG</a>
                     @if(!empty($plant->uid))
                         <a id="downloadCSV-battery" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'battery', 'csv']) }}">Download CSV</a>
                     @else
                         <span class="block px-4 py-2 text-gray-400 cursor-not-allowed" title="Plant ID missing">Download CSV</span>
                     @endif
                     <a id="downloadPDF-battery" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PDF</a>
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
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-savings" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PNG</a>
                     @if(!empty($plant->uid))
                         <a id="downloadCSV-savings" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'savings', 'csv']) }}">Download CSV</a>
                     @else
                         <span class="block px-4 py-2 text-gray-400 cursor-not-allowed" title="Plant ID missing">Download CSV</span>
                     @endif
                     <a id="downloadPDF-savings" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PDF</a>
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

// Format date for chart labels
function formatLabelDate(dateString) {
    const options = { hour: '2-digit', minute: '2-digit' };
    try {
        let date;
        // Enhanced timestamp detection and handling
        if (typeof dateString === 'number' || /^\d+$/.test(dateString)) {
            // Unix timestamp in seconds or string that looks like a number
            date = new Date(parseInt(dateString) * 1000);
        } else {
            // ISO string or other date format
            date = new Date(dateString);
        }
        
        if (isNaN(date.getTime())) {
            console.log('Invalid date encountered:', dateString);
            return dateString;
        }
        
        return date.toLocaleTimeString([], options);
    } catch (e) {
        console.log('Error formatting date:', e, dateString);
        return dateString;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // --- ENERGY CHART ---
    // Try to use plant->energy_chart, fallback to aggregated_data_snapshots if empty
    let energyData = @json($plant->energy_chart ?? []);
    let batteryPriceData = @json($plant->battery_price ?? []);
    let batterySavingsData = @json($plant->battery_savings ?? []);
    const aggregatedSnapshots = @json($plant->aggregated_data_snapshots ?? []);
    
    // Debug logging to help identify issues
    console.log('Energy Chart Data:', energyData);
    console.log('Battery Price Data:', batteryPriceData);  
    console.log('Battery Savings Data:', batterySavingsData);
    console.log('Aggregated Snapshots:', aggregatedSnapshots);
    
    // Additional debugging for timestamp format
    if (aggregatedSnapshots && aggregatedSnapshots.length > 0) {
        const firstSnapshot = aggregatedSnapshots[0];
        console.log('First snapshot timestamp details:', {
            hasTimestamp: !!firstSnapshot.timestamp,
            hasUnixDt: !!firstSnapshot.dt,
            hasTime: !!firstSnapshot.time,
            timestampValue: firstSnapshot.timestamp,
            dtValue: firstSnapshot.dt,
            timeValue: firstSnapshot.time
        });
    }
    
    // Additional debugging for timestamp format
    if (aggregatedSnapshots.length > 0) {
        const firstSnapshot = aggregatedSnapshots[0];
        console.log('First snapshot timestamp details:', {
            hasTimestamp: !!firstSnapshot.timestamp,
            hasUnixDt: !!firstSnapshot.dt,
            hasTime: !!firstSnapshot.time,
            timestampValue: firstSnapshot.timestamp,
            dtValue: firstSnapshot.dt,
            timeValue: firstSnapshot.time
        });
    }
    
    // Process the aggregated data from the API
    if (!energyData || Object.keys(energyData).length === 0) {
        console.log('Using aggregated_data_snapshots for energy chart');
        energyData = {};
        
        // Generate demo data if no real data exists (for testing purposes)
        if (aggregatedSnapshots.length === 0) {
            const now = new Date();
            for (let i = 0; i < 24; i++) {
                const timestamp = new Date(now.getTime() - (23-i) * 3600 * 1000).toISOString();
                energyData[timestamp] = {
                    pv_p: Math.random() * 5000,
                    battery_p: Math.random() * 3000 * (Math.random() > 0.5 ? 1 : -1),
                    grid_p: Math.random() * 4000 * (Math.random() > 0.3 ? 1 : -1)
                };
            }
        } else {
            // Process real data
            if (aggregatedSnapshots.forEach) {
                console.log('Processing', aggregatedSnapshots.length, 'aggregated data snapshots');
                // Process real data
                aggregatedSnapshots.forEach(row => {
                    try {
                        // Check for dt (unix timestamp) or timestamp or time fields
                        let timestamp;
                        if (row.timestamp) {
                            timestamp = row.timestamp;
                        } else if (row.dt) {
                            // Convert unix timestamp to ISO string
                            timestamp = new Date(row.dt * 1000).toISOString();
                        } else if (row.time) {
                            timestamp = new Date(row.time).toISOString();
                        }
                        
                        if (timestamp) {
                            // Calculate pv_p if it's missing but we have grid_p and battery_p
                            let pvValue = 0;
                            if (row.pv_p !== undefined) {
                                pvValue = parseFloat(row.pv_p);
                            } else if (row.grid_p !== undefined && row.battery_p !== undefined) {
                                // Approximate PV = Grid + Battery (when battery is negative/charging)
                                const gridP = parseFloat(row.grid_p);
                                const batteryP = parseFloat(row.battery_p);
                                // If battery is charging (negative), grid supplies both load and battery
                                // If battery is discharging (positive), battery helps grid supply load
                                pvValue = Math.max(0, gridP + (batteryP < 0 ? Math.abs(batteryP) : -batteryP));
                            }
                            
                            energyData[timestamp] = {
                                pv_p: pvValue,
                                battery_p: parseFloat(row.battery_p ?? 0),
                                grid_p: parseFloat(row.grid_p ?? 0)
                            };
                        }
                    } catch (e) {
                        console.error('Error processing data row:', e, row);
                    }
                });
            } else {
                console.error('aggregatedSnapshots is not iterable:', aggregatedSnapshots);
            }
        }
    }
    
    // Construct battery price data from aggregated snapshots if needed
    if (!batteryPriceData || Object.keys(batteryPriceData).length === 0) {
        console.log('Using aggregated_data_snapshots for battery price/power chart');
        batteryPriceData = {};
        
        // Generate demo data if no real data exists (for testing purposes)
        if (aggregatedSnapshots.length === 0) {
            const now = new Date();
            for (let i = 0; i < 24; i++) {
                const timestamp = new Date(now.getTime() - (23-i) * 3600 * 1000).toISOString();
                batteryPriceData[timestamp] = {
                    battery_p: Math.random() * 3000 * (Math.random() > 0.5 ? 1 : -1),
                    tariff: 0.15 + Math.random() * 0.1
                };
            }
        } else {
            // Process real data
            if (aggregatedSnapshots.forEach) {
                console.log('Processing', aggregatedSnapshots.length, 'aggregated data snapshots for battery price');
                // Process real data
                aggregatedSnapshots.forEach(row => {
                    try {
                        // Check for dt (unix timestamp) or timestamp or time fields
                        let timestamp;
                        if (row.timestamp) {
                            timestamp = row.timestamp;
                        } else if (row.dt) {
                            // Convert unix timestamp to ISO string
                            timestamp = new Date(row.dt * 1000).toISOString();
                        } else if (row.time) {
                            timestamp = new Date(row.time).toISOString();
                        }
                        
                        if (timestamp) {
                            batteryPriceData[timestamp] = {
                                battery_p: parseFloat(row.battery_p ?? 0),
                                tariff: parseFloat(row.tariff ?? 0.15) // Default tariff if missing
                            };
                        }
                    } catch (e) {
                        console.error('Error processing battery price data row:', e, row);
                    }
                });
            } else {
                console.error('aggregatedSnapshots is not iterable for battery price data:', aggregatedSnapshots);
            }
        }
    }
    
    // Construct battery savings data from aggregated snapshots if needed
    if (!batterySavingsData || Object.keys(batterySavingsData).length === 0) {
        console.log('Using aggregated_data_snapshots for battery savings chart');
        batterySavingsData = {};
        
        // Generate demo data if no real data exists (for testing purposes)
        if (aggregatedSnapshots.length === 0) {
            const now = new Date();
            for (let i = 0; i < 24; i++) {
                const timestamp = new Date(now.getTime() - (23-i) * 3600 * 1000).toISOString();
                batterySavingsData[timestamp] = {
                    battery_savings: Math.random() * 0.5 * (Math.random() > 0.3 ? 1 : -0.2)
                };
            }
        } else {
            // Process real data
            if (aggregatedSnapshots.forEach) {
                console.log('Processing', aggregatedSnapshots.length, 'aggregated data snapshots for battery savings');
                // Process real data
                aggregatedSnapshots.forEach(row => {
                    try {
                        // Check for dt (unix timestamp) or timestamp or time fields
                        let timestamp;
                        if (row.timestamp) {
                            timestamp = row.timestamp;
                        } else if (row.dt) {
                            // Convert unix timestamp to ISO string
                            timestamp = new Date(row.dt * 1000).toISOString();
                        } else if (row.time) {
                            timestamp = new Date(row.time).toISOString();
                        }
                        
                        if (timestamp) {
                            let savings = row.battery_savings !== undefined ? parseFloat(row.battery_savings) : null;
                            
                            // Calculate savings if missing but battery power and tariff are available
                            if (savings === null && row.battery_p !== undefined) {
                                const batteryPower = parseFloat(row.battery_p);
                                // Use tariff from row or default to 0.15 if not available
                                const tariff = row.tariff !== undefined ? parseFloat(row.tariff) : 0.15;
                                
                                // For this chart, we want to show savings for both charging and discharging
                                // negative battery power (charging) can also generate value in time-of-use tariff scenarios
                                const powerForSavings = Math.abs(batteryPower); // Use absolute value
                                if (powerForSavings > 0) {
                                    savings = (powerForSavings / 1000) * tariff * 0.2; // Assuming 20% efficiency gain
                                } else {
                                    savings = 0;
                                }
                            }
                            
                            batterySavingsData[timestamp] = {
                                battery_savings: savings || 0
                            };
                        }
                    } catch (e) {
                        console.error('Error processing battery savings data row:', e, row);
                    }
                });
            } else {
                console.error('aggregatedSnapshots is not iterable for battery savings data:', aggregatedSnapshots);
            }
        }
    }
    
    // --- RENDER ENERGY CHART ---
    if (Object.keys(energyData).length > 0) {
        console.log('Rendering energy chart with data:', Object.keys(energyData).length, 'entries');
        try {
            const entries = Object.entries(energyData).sort(([a], [b]) => {
                // Handle different timestamp formats for sorting
                const dateA = typeof a === 'number' || /^\d+$/.test(a) ? 
                    new Date(parseInt(a) * 1000) : new Date(a);
                const dateB = typeof b === 'number' || /^\d+$/.test(b) ? 
                    new Date(parseInt(b) * 1000) : new Date(b);
                return dateA - dateB;
            });
            
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
                        label: 'PV (kW)', 
                        data: pvData,
                        borderColor: 'rgba(0,123,255,1)', 
                        backgroundColor: 'rgba(0,123,255,0.15)', 
                        fill: true, 
                        pointRadius: 2, 
                        pointHoverRadius: 12
                    },
                    {
                        label: 'Battery (kW)', 
                        data: batteryData,
                        borderColor: 'rgba(220,53,69,1)', 
                        backgroundColor: 'rgba(220,53,69,0.12)', 
                        fill: true, 
                        pointRadius: 2, 
                        pointHoverRadius: 12
                    },
                    {
                        label: 'Grid (kW)', 
                        data: gridData,
                        borderColor: 'rgba(40,167,69,1)', 
                        backgroundColor: 'rgba(40,167,69,0.12)', 
                        fill: true, 
                        pointRadius: 2, 
                        pointHoverRadius: 12
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
                        ticks: { font: { size: 14 } },
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
        if (energyTable) {
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
        }
        } catch (e) {
            console.error('Error rendering energy chart:', e);
            document.getElementById('energyChart').parentElement.innerHTML = 
                '<div class="text-center text-gray-400 py-12">Error rendering energy chart. Check console for details.</div>';
        }
    } else {
        console.log('No energy chart data to render');
        document.getElementById('energyChart').parentElement.innerHTML = '<div class="text-center text-gray-400 py-12">No energy chart data available.</div>';
    }
    
    // --- RENDER BATTERY POWER/PRICE CHART ---
    if (Object.keys(batteryPriceData).length > 0) {
        console.log('Rendering battery price chart with data:', Object.keys(batteryPriceData).length, 'entries');
        try {
            const entries = Object.entries(batteryPriceData).sort(([a], [b]) => {
                // Handle different timestamp formats for sorting
                const dateA = typeof a === 'number' || /^\d+$/.test(a) ? 
                    new Date(parseInt(a) * 1000) : new Date(a);
                const dateB = typeof b === 'number' || /^\d+$/.test(b) ? 
                    new Date(parseInt(b) * 1000) : new Date(b);
                return dateA - dateB;
            });
            
            const labels = entries.map(([ts]) => formatLabelDate(ts));
            const batteryData = entries.map(([, v]) => v.battery_p / 1000);
            const tariffData = entries.map(([, v]) => v.tariff);
        
        const batteryCtx = document.getElementById('batteryChart');
        if (batteryCtx) {
            new Chart(batteryCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Battery Power (kW)',
                            data: batteryData,
                            borderColor: 'rgba(220,53,69,1)',
                            backgroundColor: 'rgba(220,53,69,0.1)',
                            fill: true,
                            yAxisID: 'y',
                            pointRadius: 2,
                            pointHoverRadius: 12
                        },
                        {
                            label: 'Energy Price (€/kWh)',
                            data: tariffData,
                            borderColor: 'rgba(75,192,192,1)',
                            backgroundColor: 'rgba(75,192,192,0.1)',
                            fill: false,
                            yAxisID: 'y1',
                            pointRadius: 2,
                            pointHoverRadius: 12
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
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
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Energy Price (€/kWh)'
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
                
                // Fill Battery Data Table
                const batteryTable = document.getElementById('batteryDataTableBody');
                if (batteryTable) {
                    batteryTable.innerHTML = '';
                    entries.forEach(([ts, val]) => {
                        batteryTable.innerHTML += `
                        <tr>
                            <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                            <td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td>
                            <td class="px-4 py-2 text-center">${val.tariff.toFixed(4)}</td>
                        </tr>`;
                    });
                }
            } else {
                console.error("Battery chart canvas element not found");
            }
        } catch (e) {
            console.error('Error rendering battery chart:', e);
            const batteryChartElement = document.getElementById('batteryChart');
            if (batteryChartElement) {
                batteryChartElement.parentElement.innerHTML = 
                    '<div class="text-center text-gray-400 py-12">Error rendering battery chart. Check console for details.</div>';
            }
        }
    } else {
        console.log('No battery price data to render');
        const batteryChartElement = document.getElementById('batteryChart');
        if (batteryChartElement) {
            batteryChartElement.parentElement.innerHTML = '<div class="text-center text-gray-400 py-12">No battery power data available.</div>';
        }
    }
    
    // --- RENDER BATTERY SAVINGS CHART ---
    if (Object.keys(batterySavingsData).length > 0) {
        console.log('Rendering battery savings chart with data:', Object.keys(batterySavingsData).length, 'entries');
        try {
            // BATTERY SAVINGS CHART
            const savingsEntries = Object.entries(batterySavingsData).sort(([a], [b]) => {
                // Handle different timestamp formats for sorting
                const dateA = typeof a === 'number' || /^\d+$/.test(a) ? 
                    new Date(parseInt(a) * 1000) : new Date(a);
                const dateB = typeof b === 'number' || /^\d+$/.test(b) ? 
                    new Date(parseInt(b) * 1000) : new Date(b);
                return dateA - dateB;
            });
            
            const savingsLabels = savingsEntries.map(([ts]) => formatLabelDate(ts));
            const savingsData = savingsEntries.map(([, v]) => v.battery_savings);
            const totalSavings = savingsData.reduce((acc, val) => acc + parseFloat(val || 0), 0);
        
        const batterySavingsTotal = document.getElementById('batterySavingsTotal');
        if (batterySavingsTotal) {
            batterySavingsTotal.textContent = `Your savings today: € ${totalSavings.toFixed(2)}`;
        }

        const savingsChart = document.getElementById('savingsChart');
        if (savingsChart) {
            new Chart(savingsChart, {
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
                        y: { 
                            title: {
                                display: true,
                                text: 'Battery Savings (€)'
                            },
                            type: 'linear', 
                            position: 'left', 
                            ticks: { font: { size: 14 } },
                            grid: {
                                lineWidth: 1,
                                color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                            },
                        },
                        x: { ticks: { font: { size: 14 } } }
                    }
                }
            });

            // Fill Savings Data Table
            const savingsTable = document.getElementById('batterySavingsDataTableBody');
            if (savingsTable) {
                savingsTable.innerHTML = '';
                savingsEntries.forEach(([ts, val]) => {
                    savingsTable.innerHTML += `
                    <tr>
                        <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                        <td class="px-4 py-2 text-center">${parseFloat(val.battery_savings).toFixed(2)}</td>
                    </tr>`;
                });
            }
        } else {
            console.error("Savings chart canvas element not found");
        }
        } catch (e) {
            console.error('Error rendering savings chart:', e);
            const savingsChartElement = document.getElementById('savingsChart');
            if (savingsChartElement) {
                savingsChartElement.parentElement.innerHTML = 
                    '<div class="text-center text-gray-400 py-12">Error rendering battery savings chart. Check console for details.</div>';
            }
        }
    } else {
        console.log('No battery savings data to render');
        const savingsChartElement = document.getElementById('savingsChart');
        if (savingsChartElement) {
            savingsChartElement.parentElement.innerHTML = '<div class="text-center text-gray-400 py-12">No battery savings data available.</div>';
        }
    }
});
</script>


<script>
    function sendChartToBackend(chartId, chartName, plantId, type) {
        let canvas = document.getElementById(chartId);
        let dataUrl = canvas.toDataURL('image/png');

        return fetch(`/plants/${plantId}/save-chart-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                chart: chartName,
                image: dataUrl
            })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.href = `/plants/${plantId}/download/${chartName}/${type}`;
              } else {
                  alert('Error saving chart image!');
              }
          });
    }

    document.addEventListener('DOMContentLoaded', function() {
        let plantId = @json($plant->uid);
        // ENERGY
        document.getElementById('downloadPNG-energy').addEventListener('click', function(e) {
            e.preventDefault();
            sendChartToBackend('energyChart', 'energy', plantId, 'png');
        });
        document.getElementById('downloadPDF-energy').addEventListener('click', function(e) {
            e.preventDefault();
            sendChartToBackend('energyChart', 'energy', plantId, 'pdf');
        });
        // BATTERY
        document.getElementById('downloadPNG-battery').addEventListener('click', function(e) {
            e.preventDefault();
            sendChartToBackend('batteryChart', 'battery', plantId, 'png');
        });
        document.getElementById('downloadPDF-battery').addEventListener('click', function(e) {
            e.preventDefault();
            sendChartToBackend('batteryChart', 'battery', plantId, 'pdf');
        });
        // SAVINGS
        document.getElementById('downloadPNG-savings').addEventListener('click', function(e) {
            e.preventDefault();
            sendChartToBackend('savingsChart', 'savings', plantId, 'png');
        });
        document.getElementById('downloadPDF-savings').addEventListener('click', function(e) {
            e.preventDefault();
            sendChartToBackend('savingsChart', 'savings', plantId, 'pdf');
        });
    });
    </script>
