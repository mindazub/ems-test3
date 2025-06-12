@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Plant Report - {{ $plant->name ?? $id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table th,
        .info-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        
        .info-table th {
            background-color: #f9fafb;
            font-weight: bold;
            width: 30%;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 15px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 6px;
            text-align: center;
        }
        
        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        
        .status-working { color: #10b981; font-weight: bold; }
        .status-maintenance { color: #f59e0b; font-weight: bold; }
        .status-offline { color: #ef4444; font-weight: bold; }
        
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="title">Plant Report</div>
        <div class="subtitle">
            Plant ID: {{ Str::substr($id, 0, 8) }} | 
            Date: {{ $selectedDate }} | 
            Generated: {{ $generatedAt }}
        </div>
    </div>

    <!-- Plant Information -->
    <div class="section">
        <div class="section-title">Plant Information</div>
        @if(!empty($metadata))
            <table class="info-table">
                @foreach($metadata as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>
                        @if($key === 'Status')
                            <span class="status-{{ strtolower($value) }}">{{ $value }}</span>
                        @elseif($key === 'Capacity')
                            {{ number_format($value / 1000) }} kWh
                        @elseif(in_array($key, ['Latitude', 'Longitude']))
                            {{ $value }}°
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        @else
            <p>No plant information available.</p>
        @endif
    </div>

    <!-- Data Summary -->
    @if(!empty($energyData) || !empty($batteryData) || !empty($savingsData))
    <div class="section">
        <div class="section-title">Daily Summary</div>
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">
                        {{ !empty($energyData) ? count($energyData) : 0 }}
                    </div>
                    <div class="summary-label">Energy Data Points</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">
                        {{ !empty($batteryData) ? count($batteryData) : 0 }}
                    </div>
                    <div class="summary-label">Battery Data Points</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">
                        {{ !empty($savingsData) ? count($savingsData) : 0 }}
                    </div>
                    <div class="summary-label">Savings Data Points</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Energy Data -->
    @if(!empty($energyData))
    <div class="section">
        <div class="section-title">Energy Data (First 24 entries)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>PV Power (W)</th>
                    <th>Battery Power (W)</th>
                    <th>Load Power (W)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($energyData, 0, 24) as $entry)
                <tr>
                    <td>
                        @if($user && $user->getTimeFormat() === '12')
                            {{ date('g:i A', strtotime($entry['time'] ?? '00:00')) }}
                        @else
                            {{ $entry['time'] ?? '00:00' }}
                        @endif
                    </td>
                    <td>{{ number_format($entry['pv_power'] ?? 0, 1) }}</td>
                    <td>{{ number_format($entry['battery_power'] ?? 0, 1) }}</td>
                    <td>{{ number_format($entry['load_power'] ?? 0, 1) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($energyData) > 24)
        <p style="font-style: italic; color: #666;">
            Showing first 24 of {{ count($energyData) }} total energy data points.
        </p>
        @endif
    </div>
    @endif

    <!-- Battery Data -->
    @if(!empty($batteryData))
    <div class="section">
        <div class="section-title">Battery Data (First 24 entries)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>SOC (%)</th>
                    <th>Voltage (V)</th>
                    <th>Current (A)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($batteryData, 0, 24) as $entry)
                <tr>
                    <td>
                        @if($user && $user->getTimeFormat() === '12')
                            {{ date('g:i A', strtotime($entry['time'] ?? '00:00')) }}
                        @else
                            {{ $entry['time'] ?? '00:00' }}
                        @endif
                    </td>
                    <td>{{ number_format($entry['soc'] ?? 0, 1) }}</td>
                    <td>{{ number_format($entry['voltage'] ?? 0, 1) }}</td>
                    <td>{{ number_format($entry['current'] ?? 0, 1) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($batteryData) > 24)
        <p style="font-style: italic; color: #666;">
            Showing first 24 of {{ count($batteryData) }} total battery data points.
        </p>
        @endif
    </div>
    @endif

    <!-- Savings Data -->
    @if(!empty($savingsData))
    <div class="section">
        <div class="section-title">Savings Data (First 24 entries)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Savings (€)</th>
                    <th>Grid Price (€/kWh)</th>
                    <th>Energy Saved (kWh)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($savingsData, 0, 24) as $entry)
                <tr>
                    <td>
                        @if($user && $user->getTimeFormat() === '12')
                            {{ date('g:i A', strtotime($entry['time'] ?? '00:00')) }}
                        @else
                            {{ $entry['time'] ?? '00:00' }}
                        @endif
                    </td>
                    <td>{{ number_format($entry['savings'] ?? 0, 2) }}</td>
                    <td>{{ number_format($entry['grid_price'] ?? 0, 3) }}</td>
                    <td>{{ number_format($entry['energy_saved'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($savingsData) > 24)
        <p style="font-style: italic; color: #666;">
            Showing first 24 of {{ count($savingsData) }} total savings data points.
        </p>
        @endif
    </div>
    @endif

    <!-- No Data Message -->
    @if(empty($energyData) && empty($batteryData) && empty($savingsData))
    <div class="section">
        <div class="section-title">No Data Available</div>
        <p>No chart data was found for the selected date: {{ $selectedDate }}</p>
        <p>Please ensure data exists for this plant and date before generating the report.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Plant Report Generated on {{ $generatedAt }} | Plant ID: {{ $id }}
    </div>
</body>
</html>
