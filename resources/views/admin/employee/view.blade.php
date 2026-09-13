<x-admin.header title="Hospital Waste Mnagment " />

<main class="content px-3 py-3">
    <div class="container-fluid">

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Employee Information</h3>
            <a href="{{ route('employee.pdf', $employee->id) }}" class="btn btn-success btn-lg">
                <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
            </a>
        </div>

        <div class="card shadow border-0">
            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4 text-center border-end">
                        <img src="{{ asset('public/' . $employee->employee_image) }}"
                             onerror="this.src='{{ asset('assets/images/user.png') }}'"
                             class="rounded-circle mb-3"
                             width="140" height="140">

                        <h5 class="mb-0">{{ $employee->name }}</h5>
                        <small class="text-muted">{{ $employee->employee_type }}</small>

                        <hr>

                        <p class="mb-1"><b>Status:</b>
                            <span class="badge {{ $employee->status == 'active' ? 'bg-success' : 'bg-danger' }} text-capitalize">
                                {{ $employee->status ?? '' }}
                            </span>
                        </p>

                        <p class="mb-1"><b>Gender:</b> {{ ucfirst($employee->gender) }}</p>
                        <p class="mb-1"><b>City:</b> {{ $employee->city_name }}</p>
                    </div>

                    <div class="col-md-8">
                        <h5 class="mb-3 border-bottom pb-2">Personal Details</h5>

                        <div class="row">
                            <div class="col-md-6 mb-2"><b>Phone:</b> {{ $employee->phone_no }}</div>
                            <div class="col-md-6 mb-2"><b>Email:</b> {{ $employee->email }}</div>
                            <div class="col-md-6 mb-2"><b>CNIC:</b> {{ $employee->cnic_no }}</div>
                            <div class="col-md-6 mb-2"><b>Address:</b> {{ $employee->address }}</div>
                        </div>

                        <h5 class="mt-4 mb-3 border-bottom pb-2">Salary Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2"><b>Salary:</b> Rs {{ number_format($employee->salary) }}</div>
                            <div class="col-md-6 mb-2"><b>Allowance:</b> Rs {{ number_format($employee->allowance) }}</div>
                        </div>

                        <h5 class="mt-4 mb-3 border-bottom pb-2">Bank Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2"><b>Bank:</b> {{ $employee->bank_name }}</div>
                            <div class="col-md-6 mb-2"><b>Account:</b> {{ $employee->account_no }}</div>
                            <div class="col-md-12 mb-2"><b>IBAN:</b> {{ $employee->iban }}</div>
                        </div>

                        <h5 class="mt-4 mb-3 border-bottom pb-2">Other Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2"><b>Education:</b> {{ $employee->education }}</div>
                            <div class="col-md-6 mb-2"><b>License No:</b> {{ $employee->license_no }}</div>
                            <div class="col-md-6 mb-2"><b>Security No:</b> {{ $employee->security_no }}</div>
                            <div class="col-md-6 mb-2"><b>Old Age Benefits:</b> {{ $employee->oldage_benefits }}</div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</main>

<x-admin.footer />
