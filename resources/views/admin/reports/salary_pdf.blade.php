<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>
<div style="width: 100%; text-align: center; margin-bottom: 10px; display:flex">
    <img src="{{ $image }}" alt="" style="vertical-align: middle; width: 150px;">
    <span style="display: inline-block; vertical-align: middle; font-size: 24px; font-weight: bold; margin-left: 10px;">
        {{ $setting->hospital_name ?? '' }}
    </span>
</div>

<h3>Salary Report</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Employee</th>
            <th>City</th>
            <th>Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ \Carbon\Carbon::parse($row->salary_date)->format('d-m-Y') }}</td>
            <td>{{ $row->name }}</td>
            <td>{{ $row->city_name }}</td>
            <td>{{ number_format($row->total_salary) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total:</th>
            <th>{{ number_format($totalAmount) }}</th>
        </tr>
    </tfoot>
</table>


</body>
</html>
