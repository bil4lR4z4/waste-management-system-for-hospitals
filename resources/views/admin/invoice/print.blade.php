<!DOCTYPE html>
<html>
<head>
    <title>Invoice Print</title>
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        body{
            margin: 40px;
            color: #000;
        }

        .header{
            /* text-align: center; */
        }

        .header h1{
            font-size: 26px;
            margin: 0;
            text-transform: uppercase;
        }

        .header p{
            margin: 3px 0;
            font-size: 13px;
        }

        .line{
            border-top: 2px solid #000;
            margin: 15px 0;
        }

        .info{
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td{
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th{
            background: #f2f2f2;
        }

        .total{
            /* text-align: right; */
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            justify-content: end;
        }

        .footer{
            margin-top: 60px;
        }

        .sign{
            width: 200px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        @media print {
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div style="text-align: center; display: flex; justify-content: center; gap: 10px; margin-bottom: 30px">
            <div class="">
                <img src="{{ asset('public/' . $setting->logo) }}" alt="" width="150px">
            </div>
            <div class="">
                <h1>{{ $setting->hospital_name }}</h1>
            </div>
        </div>
        <div style="padding-bottom: 30px; display: flex; justify-content: space-between">
            <div class="">
                <p>Phone: {{ auth()->user()->phone ?? $setting->phone ?? '----' }}</p>
                <p>Address: {{ auth()->user()->address ?? $setting->address ?? '----' }}</p>
                <p>NTN: {{ auth()->user()->ntn ?? $setting->ntn ?? '----' }}</p>
            </div>
            <div class="">
               <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }} <br>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between">
            <div class="">
                <h6 style="margin: 0px ; margin-bottom: 10px;">Bank Details</h6>
                <p>{{ optional(auth()->user())->bank_name ?? $setting->bank_name ?? '' }}</p>
                <p>Account No: {{ optional(auth()->user())->bank_account_no ?? $setting->bank_account_no ?? '' }}</p>
                <p>Account Holder: {{ optional(auth()->user())->bank_account_holder ?? $setting->bank_account_holder ?? '' }}</p>

                @php
                    $qr = optional(auth()->user())->bank_qr_code ?? $setting->bank_qr_code ?? null;
                @endphp

                @if ($qr)
                    <img src="{{ asset('public/'.$qr) }}" alt="" width="100px" class="mt-3">
                @endif

            </div>
            <div class="">
                <h6 style="margin: 0px ; margin-bottom: 10px;">Mobail Payment Details</h6>
                <p>Account Type: {{ optional(auth()->user())->phone_account_name ?? $setting->phone_account_name ?? '' }}</p>
                <p>Account No: {{ optional(auth()->user())->phone_account_no ?? $setting->phone_account_no ?? '' }}</p>
                <p>Account Holder: {{ optional(auth()->user())->phone_account_holder ?? $setting->phone_account_holder ?? '' }}</p>

                @php
                    $phoneQr = optional(auth()->user())->phone_qr_code ?? $setting->phone_qr_code ?? null;
                @endphp

                @if ($phoneQr)
                    <img src="{{ asset('public/'.$phoneQr) }}" alt="" width="100px" class="mt-3">
                @endif

            </div>
        </div>
    </div>

    <div class="line"></div>

    <div class="info">
        <div>
            <strong>Hospital:</strong> {{ $invoice->hospital->company_name ?? 'N/A' }} <br>
            <strong>Owner:</strong> {{ $invoice->hospital->owner_name ?? 'N/A' }} <br>
            <strong>Address:</strong> {{ $invoice->hospital->address ?? 'N/A' }}
        </div>
        <div>
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }} <br>
            <strong>Receipt No:</strong> {{ $invoice->receipt_no }} <br>
            <!-- <strong>Employee:</strong> {{ $invoice->employee->name ?? 'N/A' }} -->
        </div>
    </div>
    <div class="">
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Billing Month</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $setting->hospital_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->date)->format('M Y') }}</td>
                    <td>{{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @php
        $tax = 0;
        $companyTax = 0;
        $ourTax = 0;

        if($invoice->tax_status == 1){
            $tax = $invoice->tax; // 16% tax
            $companyTax = $tax * 0.80; // company pays 80%
            $ourTax = $tax * 0.20; // we pay 20%
        }
    @endphp

<div class="total">
    <div class="">
        @if($invoice->tax_status == 1)
            <p>Base Amount: {{ number_format($invoice->amount, 2) }}</p>

            <p>Total Tax ({{$setting->tax}}%): {{ number_format($tax, 2) }}</p>
            <p>Tax Paid by Us (80%): {{ number_format($companyTax, 2) }}</p>
            <p>Tax Paid by Company (20%): {{ number_format($ourTax, 2) }}</p>
        @endif

        <p>
            <strong>
                Total Payable: 
                {{ number_format($invoice->amount + $companyTax, 2) }}
            </strong>
        </p>
    </div>
</div>



    <div class="footer">
        <p>{{auth()->user()->first_name ?? '' }} {{auth()->user()->last_name ?? ''}}</p>
        <div class="sign">
            Signature
        </div>
    </div>

    <script>
        window.onafterprint = function() {
            window.location.href = "{{ route('invoice.index') }}";
        };
    </script>
</body>
</html>
