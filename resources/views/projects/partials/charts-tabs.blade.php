<div class="card mb-5">
    <div class="card-body">
        <h4 class="text-center">Energy Overview</h4>
        <ul class="nav nav-tabs" id="chartTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="energy-tab" data-bs-toggle="tab" data-bs-target="#energy"
                    type="button" role="tab">Energy</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="battery-tab" data-bs-toggle="tab" data-bs-target="#battery" type="button"
                    role="tab">Battery</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="savings-tab" data-bs-toggle="tab" data-bs-target="#savings" type="button"
                    role="tab">Savings</button>
            </li>
        </ul>
        <div class="tab-content pt-3">
            <div class="tab-pane fade show active" id="energy" role="tabpanel">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="energy-graph-tab" data-bs-toggle="tab"
                            data-bs-target="#energy-graph" type="button" role="tab">Graph</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="energy-data-tab" data-bs-toggle="tab" data-bs-target="#energy-data"
                            type="button" role="tab">Data</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="energy-graph" role="tabpanel">
                        <canvas id="energyChart" height="100"></canvas>
                    </div>
                    <div class="tab-pane fade" id="energy-data" role="tabpanel">
                        <table class="table table-bordered mt-3">
                            <thead>
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

            <div class="tab-pane fade" id="battery" role="tabpanel">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="battery-graph-tab" data-bs-toggle="tab"
                            data-bs-target="#battery-graph" type="button" role="tab">Graph</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="battery-data-tab" data-bs-toggle="tab"
                            data-bs-target="#battery-data" type="button" role="tab">Data</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="battery-graph" role="tabpanel">
                        <canvas id="batteryChart" height="100"></canvas>
                    </div>
                    <div class="tab-pane fade" id="battery-data" role="tabpanel">
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Battery Power (W)</th>
                                    <th>Tariff (€/kWh)</th>
                                </tr>
                            </thead>
                            <tbody id="batteryDataTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="savings" role="tabpanel">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="savings-graph-tab" data-bs-toggle="tab"
                            data-bs-target="#savings-graph" type="button" role="tab">Graph</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="savings-data-tab" data-bs-toggle="tab"
                            data-bs-target="#savings-data" type="button" role="tab">Data</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="savings-graph" role="tabpanel">
                        <p id="batteryEarningDisplay" class="text-center fw-bold animate-flash">Total Earnings:
                            calculating...</p>
                        <canvas id="batterySavingsChart" height="100"></canvas>
                    </div>
                    <div class="tab-pane fade" id="savings-data" role="tabpanel">
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Savings (€)</th>
                                </tr>
                            </thead>
                            <tbody id="savingsDataTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
