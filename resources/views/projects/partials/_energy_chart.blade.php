<div class="card mb-5">
    <div class="card-header">Energy Chart</div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="energyTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="energy-graph-tab" data-bs-toggle="tab" data-bs-target="#energy-graph"
                    type="button" role="tab" aria-controls="energy-graph" aria-selected="true">Graph</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="energy-data-tab" data-bs-toggle="tab" data-bs-target="#energy-data"
                    type="button" role="tab" aria-controls="energy-data" aria-selected="false">Data</button>
            </li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="energy-graph" role="tabpanel" aria-labelledby="energy-graph-tab">
                <canvas id="energyChart" height="100"></canvas>
            </div>
            <div class="tab-pane fade" id="energy-data" role="tabpanel" aria-labelledby="energy-data-tab">
                <button class="btn btn-outline-primary btn-sm mb-3">Download Excel or PDF</button>
                <table class="table table-bordered">
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
</div>
