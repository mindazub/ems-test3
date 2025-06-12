<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $plant->name }} - Comprehensive Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #4f46e5;
            margin: 0;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #6b7280;
            padding: 5px 15px 5px 0;
            width: 30%;
            vertical-align: top;
        }
        
        .info-value {
            display: table-cell;
            color: #111827;
            padding: 5px 0;
            vertical-align: top;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-working {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-maintenance {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-offline {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .chart-section {
            margin-top: 20px;
        }
        
        .chart-image {
            width: 100%;
            max-width: 700px;
            height: auto;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .data-table th {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
            font-weight: bold;
        }
        
        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 6px;
        }
        
        .summary-stats {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stats-cell {
            display: table-cell;
            padding: 5px 10px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }
        
        .stats-cell:last-child {
            border-right: none;
        }
        
        .stats-label {
            font-size: 10px;
            color: #64748b;
            font-weight: bold;
        }
        
        .stats-value {
            font-size: 14px;
            color: #1e293b;
            font-weight: bold;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $plant->name ?? $plant->uid }}</h1>
        <div class="subtitle">Comprehensive Plant Report - {{ $selectedDate }}</div>
        <div class="subtitle">Generated on {{ $generatedAt }}</div>
    </div>

    <!-- Plant Information Section -->
    <div class="section">
        <div class="section-title">Plant Information</div>
        
        @if(!empty($plant->metadata_flat))
            <div class="info-grid">
                @foreach($plant->metadata_flat as $key => $value)
                    <div class="info-row">
                        <div class="info-label">{{ $key }}:</div>
                        <div class="info-value">
                            @if($key === 'Status')
                                @if($value === 'Working')
                                    <span class="status-badge status-working">{{ $value }}</span>
                                @elseif($value === 'Maintenance')
                                    <span class="status-badge status-maintenance">{{ $value }}</span>
                                @elseif($value === 'Offline')
                                    <span class="status-badge status-offline">{{ $value }}</span>
                                @else
                                    {{ $value }}
                                @endif
                            @elseif($key === 'Capacity')
                                {{ number_format($value / 1000) }} kWh
                            @elseif($key === 'Owner Email')
                                {{ $value }}
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    @if(!empty($data))
        <div class="section">
            <div class="section-title">Daily Summary Statistics</div>
            
            <div class="summary-stats">
                <div class="stats-grid">
                    <div class="stats-row">
                        @if(!empty($data['energy_chart']))
                            @php
                                $totalSolar = array_sum(array_column($data['energy_chart'], 'solar_p')) / 1000;
                                $totalGrid = array_sum(array_column($data['energy_chart'], 'grid_p')) / 1000;
                                $totalLoad = array_sum(array_column($data['energy_chart'], 'load_p')) / 1000;
                            @endphp
                            <div class="stats-cell">
                                <div class="stats-label">Total Solar</div>
                                <div class="stats-value">{{ number_format($totalSolar, 1) }} kWh</div>
                            </div>
                            <div class="stats-cell">
                                <div class="stats-label">Total Grid</div>
                                <div class="stats-value">{{ number_format($totalGrid, 1) }} kWh</div>
                            </div>
                            <div class="stats-cell">
                                <div class="stats-label">Total Load</div>
                                <div class="stats-value">{{ number_format($totalLoad, 1) }} kWh</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['battery_savings']))
                            @php
                                $totalSavings = array_sum(array_column($data['battery_savings'], 'battery_savings'));
                            @endphp
                            <div class="stats-cell">
                                <div class="stats-label">Total Savings</div>
                                <div class="stats-value">${{ number_format($totalSavings, 2) }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Chart Images Section -->
    <div class="section page-break">
        <div class="section-title">Energy Charts</div>
        
        @php
            $chartTypes = [
                'energy' => 'Energy Flow Chart',
                'battery' => 'Battery Performance Chart', 
                'savings' => 'Battery Savings Chart'
            ];
        @endphp
        
        @foreach($chartTypes as $chartType => $chartTitle)
            @php
                $imagePath = public_path("charts/{$plant->uid}_{$chartType}.png");
            @endphp
            
            @if(file_exists($imagePath))
                <div class="chart-section">
                    <h3>{{ $chartTitle }}</h3>
                    <img src="{{ $imagePath }}" alt="{{ $chartTitle }}" class="chart-image">
                </div>
            @endif
        @endforeach
    </div>

    <!-- Data Tables Section -->
    @if(!empty($data))
        <div class="section page-break">
            <div class="section-title">Detailed Data</div>
            
            @if(!empty($data['energy_chart']))
                <h3>Energy Data</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Solar Power (kW)</th>
                            <th>Grid Power (kW)</th>
                            <th>Load Power (kW)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['energy_chart'] as $timestamp => $values)
                            <tr>
                                <td>
                                    @if($user && $user->getTimeFormat() === '12')
                                        {{ date('g:i A', strtotime($timestamp)) }}
                                    @else
                                        {{ date('H:i', strtotime($timestamp)) }}
                                    @endif
                                </td>
                                <td>{{ number_format(($values['solar_p'] ?? 0) / 1000, 2) }}</td>
                                <td>{{ number_format(($values['grid_p'] ?? 0) / 1000, 2) }}</td>
                                <td>{{ number_format(($values['load_p'] ?? 0) / 1000, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            @if(!empty($data['battery_price']))
                <h3 style="margin-top: 25px;">Battery Data</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Battery Power (kW)</th>
                            <th>Tariff Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['battery_price'] as $timestamp => $values)
                            <tr>
                                <td>
                                    @if($user && $user->getTimeFormat() === '12')
                                        {{ date('g:i A', strtotime($timestamp)) }}
                                    @else
                                        {{ date('H:i', strtotime($timestamp)) }}
                                    @endif
                                </td>
                                <td>{{ number_format(($values['battery_p'] ?? 0) / 1000, 2) }}</td>
                                <td>{{ number_format($values['tariff'] ?? 0, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            @if(!empty($data['battery_savings']))
                <h3 style="margin-top: 25px;">Savings Data</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Battery Savings ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['battery_savings'] as $timestamp => $values)
                            <tr>
                                <td>
                                    @if($user && $user->getTimeFormat() === '12')
                                        {{ date('g:i A', strtotime($timestamp)) }}
                                    @else
                                        {{ date('H:i', strtotime($timestamp)) }}
                                    @endif
                                </td>
                                <td>{{ number_format($values['battery_savings'] ?? 0, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Generated by EDIS Lab EMS • {{ $generatedAt }} • Plant: {{ $plant->uid }}
    </div>
</body>
</html>
