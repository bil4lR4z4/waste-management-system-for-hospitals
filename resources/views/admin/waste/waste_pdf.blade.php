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
        <h5>Waste Information</h5>
        <table>
            <tr>
                <td class="label">Date</td>
                <td colspan="3">{{ $waste->date ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Receipt No</td>
                <td colspan="3">{{ $waste->receipt_no ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">City</td>
                <td colspan="3">{{ $waste->city ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Company</td>
                <td colspan="3">{{ optional($waste->hospital)->company_name ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Employee</td>
                <td colspan="3">{{ optional($waste->employee)->name ?? '-' }}</td>
            </tr>

            <tr>
                <td colspan="4" style="background:#2c3e50;color:#fff;font-weight:bold;text-align:center;">
                    Waste Details
                </td>
            </tr>


            <tr>
                <td class="label">Total Weight</td>
                <td>{{ $waste->total_weight ?? '-' }} Kg</td>
                <td class="label">Total Bags</td>
                <td>{{ $waste->total_bags ?? '-' }}</td>  
            </tr>

            <tr>
                <td class="label">Infections</td>
                <td>{{ $waste->infections ?? '-' }}</td>
                <td class="label">Bags</td>
                <td>{{ $waste->infections_bags ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Pathological</td>
                <td>{{ $waste->pathological ?? '-' }}</td>
                <td class="label">Bags</td>
                <td>{{ $waste->pathological_bags ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Chemical</td>
                <td>{{ $waste->chemical ?? '-' }}</td>
                <td class="label">Bags</td>
                <td>{{ $waste->chemical_bags ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Pharmaceutical</td>
                <td>{{ $waste->pharmaceutical ?? '-' }}</td>
                <td class="label">Bags</td>
                <td>{{ $waste->pharmaceutical_bags ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Sharp</td>
                <td>{{ $waste->sharp ?? '-' }}</td>
                <td class="label">Bags</td>
                <td>{{ $waste->sharp_bags ?? '-' }}</td>
            </tr>

        </table>
    </div>


</body>
</html>
