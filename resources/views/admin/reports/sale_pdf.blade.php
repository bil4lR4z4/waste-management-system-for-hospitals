<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 11px; }
        h2 , h3{ text-align:center; margin-bottom:10px; }
        table { width: 100%; border-collapse: collapse; margin-top:10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #eee; }
        .total { text-align: right; font-weight: bold; margin-top: 10px; }
        tr { page-break-inside: avoid; }
        tbody tr:nth-child(25n) { page-break-after: always; } /* 25 rows per page */
    </style>
</head>
<body>

<div style="width: 100%; text-align: center; margin-bottom: 10px; display:flex">
    <img src="{{ $image }}" alt="" style="vertical-align: middle; width: 150px;">
    <span style="display: inline-block; vertical-align: middle; font-size: 24px; font-weight: bold; margin-left: 10px;">
        {{ $setting->hospital_name ?? '' }}
    </span>
</div>
<h3>Sale Report</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Company Name</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
            <td>{{ $row->company_name }}</td>
            <td>{{ number_format($row->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p class="total">
    Total Amount: {{ number_format($totalAmount, 2) }}
</p>

</body>
</html>
