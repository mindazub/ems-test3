<div class="mb-6 space-y-8">

    <!-- Energy Chart Tabs -->
    <div x-data="{ tab: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tab === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tab = 'graph'">
                    Graph
                </button>
                <button
                    :class="tab === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tab = 'data'">
                    Data
                </button>
            </nav>
            <!-- Dropdown, aligned right -->
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    {{-- Heroicon: Arrow Down Tray --}}
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
            <div x-show="tab === 'graph'">
                <h4 class="text-center mb-3 font-semibold">Energy Live Chart</h4>
                <div class="flex items-center" style="height: 400px;">
                    <canvas id="energyChart" class="w-full h-96"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tab === 'data'">
                <div class="overflow-x-auto h-96">
                    <table class="min-w-full text-xs border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left">Time</th>
                            <th class="px-4 py-2 text-left">PV (kW)</th>
                            <th class="px-4 py-2 text-left">Battery (kW)</th>
                            <th class="px-4 py-2 text-left">Grid (kW)</th>
                        </tr>
                        </thead>
                        <tbody id="energyDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Battery Chart Tabs -->
    <div x-data="{ tab: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tab === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tab = 'graph'">
                    Graph
                </button>
                <button
                    :class="tab === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tab = 'data'">
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
            <!-- Graph Tab -->
            <div x-show="tab === 'graph'">
                <h4 class="text-center mb-3 font-semibold">Battery Power and Energy Price</h4>
                <div class="flex items-center" style="height: 400px;">
                    <canvas id="batteryChart" class="w-full h-96"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tab === 'data'">
                <div class="overflow-x-auto h-96">
                    <table class="min-w-full text-xs border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left">Time</th>
                            <th class="px-4 py-2 text-left">Battery Power (kW)</th>
                            <th class="px-4 py-2 text-left">Energy Price (€ / kWh)</th>
                        </tr>
                        </thead>
                        <tbody id="batteryDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Battery Savings Chart Tabs -->
    <div x-data="{ tab: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tab === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tab = 'graph'">
                    Graph
                </button>
                <button
                    :class="tab === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tab = 'data'">
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
            <div x-show="tab === 'graph'">
                <h4 class="text-center mb-3 font-semibold">Battery Savings</h4>
                <p id="batterySavingsTotal" class="text-center text-green-700 font-semibold"></p>
                <div class="flex items-center" style="height: 400px;">
                    <canvas id="savingsChart" class="w-full h-96"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tab === 'data'">
                <div class="overflow-x-auto h-96">
                    <table class="min-w-full text-xs border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left">Time</th>
                            <th class="px-4 py-2 text-left">Battery Savings (€)</th>
                        </tr>
                        </thead>
                        <tbody id="batterySavingsDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
