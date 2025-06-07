<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $plant->name }} - {{ ucfirst($chart) }} Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        .header { border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        h1 { font-size: 20px; color: #333; margin: 0 0 5px 0; }
        h2 { font-size: 16px; margin-bottom: 10px; color: #0066cc; }
        .plant-info { color: #666; margin-bottom: 5px; }
        .date-info { color: #888; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        img { max-width: 100%; height: auto; margin: 15px 0; border: 1px solid #eee; }
        .summary-box { background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
        .summary-title { font-weight: bold; margin-bottom: 5px; }
        .summary-item { margin: 3px 0; }
        .footer { margin-top: 20px; font-size: 10px; color: #999; text-align: center; }
        .positive { color: green; }
        .negative { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($chart) }} Report</h1>
        <div class="plant-info">Plant: <strong>{{ $plant->name }}</strong></div>
        <div class="date-info">Generated on: {{ $generatedDate }}</div>
    </div>

    <div class="chart-container">
        <h2>{{ ucfirst($chart) }} Chart</h2>
        <img src="{{ $image }}" alt="Chart Image">
    </div>

    <div class="summary-box">
        <div class="summary-title">Summary</div>
        @if($chart === 'energy')
            <div class="summary-item">Total PV: <strong>{{ number_format($summary['totalPV'], 2) }} kWh</strong></div>
            <div class="summary-item">Total Battery: <strong>{{ number_format($summary['totalBattery'], 2) }} kWh</strong></div>
            <div class="summary-item">Total Grid: <strong>{{ number_format($summary['totalGrid'], 2) }} kWh</strong></div>
        @elseif($chart === 'battery')
            <div class="summary-item">Battery Charged: <strong class="positive">{{ number_format($summary['batteryCharged'], 2) }} kWh</strong></div>
            <div class="summary-item">Battery Discharged: <strong class="negative">{{ number_format($summary['batteryDischarged'], 2) }} kWh</strong></div>
            <div class="summary-item">Average Energy Price: <strong>{{ number_format($summary['avgTariff'], 4) }} €/kWh</strong></div>
        @elseif($chart === 'savings')
            <div class="summary-item">Total Savings: <strong class="positive">{{ number_format($summary['totalSavings'], 2) }} €</strong></div>
        @endif
    </div>

    <h2>Data Table</h2>
    <table>
        <thead>
            <tr>
                @if($chart === 'energy')
                    <th>Time</th>
                    <th>PV (kW)</th>
                    <th>Battery (kW)</th>
                    <th>Grid (kW)</th>
                @elseif($chart === 'battery')
                    <th>Time</th>
                    <th>Battery Power (kW)</th>
                    <th>Energy Price (€/kWh)</th>
                @elseif($chart === 'savings')
                    <th>Time</th>
                    <th>Battery Savings (€)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $ts => $val)
                <tr>
                    <td>{{ $ts }}</td>
                    @if($chart === 'energy')
                        <td>{{ $val['pv_p'] }}</td>
                        <td>{{ $val['battery_p'] }}</td>
                        <td>{{ $val['grid_p'] }}</td>
                    @elseif($chart === 'battery')
                        <td>{{ $val['battery_p'] }}</td>
                        <td>{{ $val['tariff'] }}</td>
                    @elseif($chart === 'savings')
                        <td>{{ $val['battery_savings'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
