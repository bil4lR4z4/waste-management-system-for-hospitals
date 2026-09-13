<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 11px; }
        h2 , h3{ text-align:center; margin-bottom:10px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:5px; }
        th { background:#eee; }
        .total { text-align:right; font-weight:bold; margin-top:10px; }
    </style>
</head>
<body>

<div style="width: 100%; text-align: center; margin-bottom: 10px; display:flex">
    <img src="{{ $image }}" alt="" style="vertical-align: middle; width: 150px;">
    <span style="display: inline-block; vertical-align: middle; font-size: 24px; font-weight: bold; margin-left: 10px;">
        {{ $setting->hospital_name ?? '' }}
    </span>
</div>
<h3>Expense Report</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Expense Type</th>
            <th>City</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
            <td>{{ $row->name }}</td>
            <td>{{ $row->city_name ?? 'PK' }}</td>
            <td>{{ number_format($row->amount,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p class="total">
    Total Amount: {{ number_format($totalAmount,2) }}
</p>

</body>
</html>
