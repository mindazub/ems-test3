<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $plant->name }} - {{ ucfirst($chart) }} Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { font-size: 16px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        img { max-width: 100%; height: auto; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>{{ ucfirst($chart) }} Report - {{ $plant->name }}</h2>

    <img src="{{ $image }}" alt="Chart Image">

    <table>
        <thead>
            <tr>
                @if($chart === 'energy')
                    <th>Time</th><th>PV</th><th>Battery</th><th>Grid</th>
                @elseif($chart === 'battery')
                    <th>Time</th><th>Battery Power</th><th>Tariff</th>
                @elseif($chart === 'savings')
                    <th>Time</th><th>Battery Savings</th>
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
