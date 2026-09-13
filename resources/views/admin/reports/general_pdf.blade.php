<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 10px; }
        h2 , h3{ text-align:center; margin-bottom:10px; }
        table { width: 100%; border-collapse: collapse; margin-top:10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background: #eee; }
        tr { page-break-inside: avoid; }
        tbody tr:nth-child(20n) { page-break-after: always; }
        .negative { color: red; }
        .positive { color: green; }
    </style>
</head>
<body>

<div style="width: 100%; text-align: center; margin-bottom: 10px;">
    <img src="{{ $image }}" alt="" style="vertical-align: middle; width: 150px;">
    <span style="display: inline-block; vertical-align: middle; font-size: 24px; font-weight: bold; margin-left: 10px;">
        {{ $setting->hospital_name ?? '' }}
    </span>
</div>


<h3>General Report</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Payment Type</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Running Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
            <td>{{ $row->payment_type }}</td>
            <td>{{ number_format($row->credit, 2) }}</td>
            <td>{{ number_format($row->debit, 2) }}</td>
            <td class="{{ $row->running_balance >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($row->running_balance, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
