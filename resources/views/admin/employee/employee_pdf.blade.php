<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Employee Profile</title>

<style>
    body{
        font-family: DejaVu Sans, sans-serif;
        font-size:12px;
        color:#000;
        margin:30px;
    }

    /* HEADER */
    .top{
        margin-bottom:15px;
        border-bottom:2px solid #2c3e50;
        padding-bottom:10px;
    }

    .top table{
        width:100%;
        border-collapse:collapse;
        border:none;
    }

    .top td{
        padding:7px;
        border:none; 
    }


    .top img{
        width:130px;
    }

    .top .name{
        font-size:22px;
        font-weight:bold;
        color:#2c3e50;
    }

    .sub{
        font-size:13px;
        color:#555;
    }

    .container{
        width:100%;
        margin-top:20px;
    }

    .left{
        width:30%;
        float:left;
        text-align:center;
    }

    .left img{
        width:160px;
        height:180px;
        border:2px solid #2c3e50;
        border-radius:5px;
    }

    .badge{
        padding:5px 10px;
        background:#2c3e50;
        color:#fff;
        display:inline-block;
        border-radius:4px;
        font-size:11px;
    }

    .right{
        width:68%;
        float:right;
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    td{
        padding:7px;
        border:1px solid #ddd;
    }

    td.label{
        width:30%;
        background:#f2f2f2;
        font-weight:bold;
    }

</style>
</head>

<body>
<div class="top">
    <table width="100%" border="0">
        <tr>
            <td width="20%" align="left">
                @if($setting->logo)
                    <img src="{{ public_path($setting->logo) }}">
                @endif
            </td>

            <td width="80%" align="center">
                <div class="name">{{ $setting->hospital_name }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="container">

    <div class="left">
        @if($employee->employee_image)
            <img src="{{ public_path($employee->employee_image) }}">
        @else
            <img src="{{ public_path('images/User.png') }}">
        @endif
    </div>

    <div class="right">
        <table>
            <tr>
                <td class="label">Name</td>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <td class="label">CNIC</td>
                <td>{{ $employee->cnic_no }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td>{{ $employee->phone_no }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>{{ $employee->email }}</td>
            </tr>
            <tr>
                <td class="label">Gender</td>
                <td>{{ ucfirst($employee->gender) }}</td>
            </tr>
            <tr>
                <td class="label">Address</td>
                <td>{{ $employee->address }}</td>
            </tr>
            <tr>
                <td class="label">City</td>
                <td>{{ $employee->city }}</td>
            </tr>
            <tr>
                <td class="label">Salary</td>
                <td>Rs. {{ number_format($employee->salary) }}</td>
            </tr>
            <tr>
                <td class="label">Allowance</td>
                <td>Rs. {{ number_format($employee->allowance) }}</td>
            </tr>
            <tr>
                <td class="label">Employee Type</td>
                <td>{{ $employee->employee_type }}</td>
            </tr>
            <tr>
                <td class="label">Oldage Benefit</td>
                <td>{{ $employee->oldage_benefits }}</td>
            </tr>
            @if($employee->employee_type == 'Driver')
            <tr>
                <td class="label">License No</td>
                <td>{{ $employee->license_no }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">SS No</td>
                <td>{{ $employee->security_no }}</td>
            </tr>
            <tr>
                <td class="label">Bank</td>
                <td>{{ $employee->bank_name }}</td>
            </tr>
            <tr>
                <td class="label">Account No</td>
                <td>{{ $employee->account_no }}</td>
            </tr>
            <tr>
                <td class="label">IBAN</td>
                <td>{{ $employee->iban }}</td>
            </tr>
            <tr>
                <td class="label">Education</td>
                <td>{{ $employee->education }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td style="text-transform:capitalize">{{ $employee->status }}</td>
            </tr>
            @if($employee->status == 'inactive')
                <tr>
                    <td class="label">Reason</td>
                    <td>{{ $employee->reason }}</td>
                </tr>
            @endif
        </table>
    </div>

</div>

</body>
</html>
