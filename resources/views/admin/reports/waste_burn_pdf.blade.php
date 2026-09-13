<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 10px; }
        h2 , h3{ text-align:center; margin-bottom:10px; }
        table { width: 100%; border-collapse: collapse; margin-top:10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background: #eee; }
        .total { text-align: right; font-weight: bold; margin-top: 10px; }
        tr { page-break-inside: avoid; }
        tbody tr:nth-child(20n) { page-break-after: always; } /* 20 rows per page */
    </style>
</head>
<body>
<div style="width: 100%; text-align: center; margin-bottom: 10px;">
    <img src="{{ $image }}" alt="" style="vertical-align: middle; width: 150px;">
    <span style="display: inline-block; vertical-align: middle; font-size: 24px; font-weight: bold; margin-left: 10px;">
        {{ $setting->hospital_name ?? '' }}
    </span>
</div>

<h3>Disposal Waste Report</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th scope="col">Date</th>
            <th scope="col">City Name</th>
            <th scope="col">Infections (Bags)</th>
            <th scope="col">Sharp (Bags)</th>
            <th scope="col">Chemical (Bags)</th>
            <th scope="col">Pharmaceutical (Bags)</th>
            <th scope="col">Pathological (Bags)</th>
            <th scope="col">Total Weight</th>
        </tr>
    </thead>
    <tbody>
        @foreach($wastes as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
            <td>{{ $row->city_name }}</td>
            <td>{{ $row->infections }} KG ({{ $row->infections_bags ?? '0' }})</td>
            <td>{{ $row->sharp }} KG ({{ $row->sharp_bags ?? '0' }})</td>
            <td>{{ $row->chemical }} KG ({{ $row->chemical_bags ?? '0' }})</td>
            <td>{{ $row->pharmaceutical }} KG ({{ $row->pharmaceutical_bags ?? '0' }})</td>
            <td>{{ $row->pathological }} KG ({{ $row->pathological_bags ?? '0' }})</td>
            <td>{{ $row->total_weight }} KG ({{ $row->total_bags ?? '0' }})</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total:</th>
            <th>{{ $totals['infections'] }} KG ({{ $totals['infections_bags'] }})</th>
            <th>{{ $totals['sharp'] }} KG ({{ $totals['sharp_bags'] }})</th>
            <th>{{ $totals['chemical'] }} KG ({{ $totals['chemical_bags'] }})</th>
            <th>{{ $totals['pharmaceutical'] }} KG ({{ $totals['pharmaceutical_bags'] }})</th>
            <th>{{ $totals['pathological'] }} KG ({{ $totals['pathological_bags'] }})</th>
            <th>{{ $totals['total_weight'] }} KG ({{ $totals['total_bags'] }})</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
