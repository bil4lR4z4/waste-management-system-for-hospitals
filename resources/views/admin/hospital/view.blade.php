<x-admin.header title="Hospital Waste Management" />

<main class="content px-3 py-3">
    <div class="container-fluid">

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ $company->company_name }}</h3>
            <a href="{{ route('hospital.pdf', $company->id) }}" class="btn btn-success btn-lg">
                <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
            </a>
        </div>

        <div class="card shadow border-0">
            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4 border-end">
                        <h5 class="border-bottom pb-2">Basic Info</h5>
                        <p><b>Owner:</b> {{ $company->owner_name }}</p>
                        <p><b>Email:</b> {{ $company->email ?? '' }}</p>
                        <p><b>Contact:</b> {{ $company->contact_number }}</p>
                        <p><b>City:</b> {{ $company->city->name ?? '-' }}</p>
                        <p><b>Address:</b> {{ $company->address ?? '-'}}</p>

                        <p><b>Status:</b>
                            @php
                                use Carbon\Carbon;
                                $today = Carbon::today();
                            @endphp

                            @if(!empty($company->expiry_date) && $today->gt($company->expiry_date))
                                <span class="badge bg-danger">Expired</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </p>
                    </div>

                    <div class="col-md-8">

                        <h5 class="border-bottom pb-2">Registration & Legal</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2"><b>Reg No:</b> {{ $company->registration_number ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>NTN:</b> {{ $company->ntn ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>PHC:</b> {{ $company->phcb ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>PRA:</b> {{ $company->pra ?? '-' }}</div>
                            <!-- <div class="col-md-6 mb-2"><b>SECP:</b> {{ $company->secp ?? '-' }}</div> -->
                        </div>

                        <h5 class="border-bottom pb-2">Facilities</h5>
                        <div class="w-100">
                            @php
                                $facilities = explode(',', $company->facilities ?? '');
                            @endphp

                            @if(!empty($company->facilities))
                                <div class="mt-2">
                                    @foreach($facilities as $facility)
                                        <span class="badge bg-info text-dark me-1 mb-1">
                                            {{ ucwords(trim($facility)) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No facilities added</span>
                            @endif

                        </div>

                        <h5 class="mt-4 border-bottom pb-2">Waste Management</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2"><b>Waste Volume:</b> {{ $company->waste_generation_volume ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>Waste Type:</b> {{ $company->waste_type ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>Collection Frequency:</b> {{ $company->preferred_waste_collection_frequency ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>Temp Storage:</b> {{ $company->has_temporary_waste_storage ? 'Yes' : 'No' }}</div>
                            <div class="col-md-12 mb-2"><b>Storage Method:</b> {{ $company->waste_storage_method ?? '-' }}</div>
                        </div>

                        <h5 class="mt-4 border-bottom pb-2">Contract Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2"><b>Start Date:</b> {{ $company->date ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>Expiry Date:</b> {{ $company->expiry_date ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>Payment:</b> {{ $company->payment ?? '-' }}</div>
                            <div class="col-md-6 mb-2"><b>No of Beds:</b> {{ $company->number_of_beds ?? '-' }}</div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</main>

<x-admin.footer />
