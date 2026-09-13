<x-admin.header title="Hospital Waste Management" />

<main class="content px-3 py-3">
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Invoice Details</h3>
        <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-success btn-lg"><i class="fa fa-print"></i> Print Invoice</a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">

            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="fw-bold">{{ $invoice->hospital->company_name ?? '-' }}</h4>
                    <p class="mb-1"><strong>Owner:</strong> {{ $invoice->hospital->owner_name ?? '-' }}</p>
                    <p class="mb-1"><strong>Address:</strong> {{ $invoice->hospital->address ?? '-' }}</p>
                    <p class="mb-1"><strong>City:</strong> {{ $invoice->hospital->city->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $invoice->hospital->contact_number ?? '-' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $invoice->hospital->email ?? '-' }}</p>
                </div>

                <div class="col-md-6 text-md-end">
                    <h5 class="fw-bold">Receipt #{{ $invoice->receipt_no }}</h5>
                    <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</p>
                    <p class="mb-1"><strong>Employee:</strong> {{ $invoice->employee->name ?? '-' }}</p>
                </div>
            </div>

            <hr>

            @php
                $tax = 0;
                $companyTax = 0;
                $ourTax = 0;

                if($invoice->tax_status == 1){
                    $tax = $invoice->tax;
                    $companyTax = $tax * 0.80;
                    $ourTax = $tax * 0.20;
                }
            @endphp

            <div class="d-flex justify-content-end">
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

        </div>
    </div>
</div>
</main>

<x-admin.footer />

