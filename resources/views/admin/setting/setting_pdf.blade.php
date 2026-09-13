<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Company Profile</title>

<style>
    body{
        font-family: DejaVu Sans, sans-serif;
        font-size:12px;
        color:#000;
        margin:35px;
    }

    /* HEADER */
    .top{
        text-align:center;
        margin-bottom:15px;
    }

    .top img{
        height:70px;
        margin-bottom:5px;
    }

    .name{
        font-size:24px;
        font-weight:700;
        letter-spacing:1px;
    }

    /* INFO BLOCK */
    .info{
        margin-top:10px;
        line-height:1.8;
    }

    .info p{
        margin:0px !important
    }

    .label{
        font-weight:600;
    }

    /* SECTION */
    .section{
        margin-top:20px;
    }

    .flex{
        display:flex;
        justify-content:space-between;
        gap:40px;
    }

    .col{
        width:48%;
    }


    .title{
        font-size:14px;
        font-weight:700;
        margin-bottom:5px;
        border-bottom:1px solid #000;
        width:200px;
    }

    .qr img{
        height:90px;
        margin-top:5px;
    }

    .footer{
        text-align:center;
        font-size:10px;
        margin-top:30px;
        color:#666;
    }
</style>
</head>

<body>

<!-- TOP -->
<div class="top">
    @if($setting->logo)
        <img src="{{ public_path($setting->logo) }}">
    @endif
    <div class="name">{{ $setting->hospital_name }}</div>
</div>

<!-- LEFT INFO -->
<div class="info">
    <p><strong>Address:</strong> {{ $setting->address }}</p>
    <p><strong>Phone:</strong> {{ $setting->phone }}</p>
    <p><strong>NTN:</strong> {{ $setting->ntn }}</p>
    <p><strong>PRA Number:</strong> {{ $setting->pra }}</p>
    <p><strong>SECP Number:</strong> {{ $setting->secp }}</p>
</div>

<table width="100%" style="margin-top:20px;">
    <tr>
        <!-- BANK -->
        <td width="50%" valign="top">
            <div class="title">Bank Details</div>
            <div><span class="label">Bank Name:</span> {{ $setting->bank_name }}</div>
            <div><span class="label">Account No:</span> {{ $setting->bank_account_no }}</div>
            <div><span class="label">Account Holder:</span> {{ $setting->bank_account_holder }}</div>

            @if($setting->bank_qr_code)
                <div class="qr">
                    <img src="{{ public_path($setting->bank_qr_code) }}">
                </div>
            @endif
        </td>

        <!-- MOBILE -->
        <td width="50%" valign="top">
            <div class="title">Mobile Payment</div>
            <div><span class="label">Account Name:</span> {{ $setting->phone_account_name }}</div>
            <div><span class="label">Account No:</span> {{ $setting->phone_account_no }}</div>
            <div><span class="label">Account Holder:</span> {{ $setting->phone_account_holder }}</div>

            @if($setting->phone_qr_code)
                <div class="qr">
                    <img src="{{ public_path($setting->phone_qr_code) }}">
                </div>
            @endif
        </td>
    </tr>
</table>

<div class="footer">
    Generated on {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>
