<div class="card mb-5">
    <div class="card-header">Battery + Tariff Chart</div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="batteryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="battery-graph-tab" data-bs-toggle="tab"
                    data-bs-target="#battery-graph" type="button" role="tab" aria-controls="battery-graph"
                    aria-selected="true">Graph</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="battery-data-tab" data-bs-toggle="tab" data-bs-target="#battery-data"
                    type="button" role="tab" aria-controls="battery-data" aria-selected="false">Data</button>
            </li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="battery-graph" role="tabpanel"
                aria-labelledby="battery-graph-tab">
                <canvas id="batteryChart" height="100"></canvas>
            </div>
            <div class="tab-pane fade" id="battery-data" role="tabpanel" aria-labelledby="battery-data-tab">
                <button class="btn btn-outline-primary btn-sm mb-3">Download Excel or PDF</button>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Battery (W)</th>
                            <th>Tariff (€/kWh)</th>
                        </tr>
                    </thead>
                    <tbody id="batteryDataTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
