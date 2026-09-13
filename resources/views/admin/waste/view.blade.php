<x-admin.header title="Hospital Waste Management" />

<main class="content px-3 py-3">
    <div class="container-fluid">

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                Waste Receipt #{{ $waste->receipt_no }}
            </h3>
            <a href="{{ route('waste.pdf', $waste->id) }}" class="btn btn-success btn-lg"> <i class="fa-solid fa-file-pdf me-1"></i> Download PDF </a>
        </div>

        <div class="card shadow border-0">
            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4 border-end">
                        <h5 class="border-bottom pb-2">Hospital Info</h5>
                        <p><b>Company:</b> {{ $waste->hospital->company_name ?? '-' }}</p>
                        <p><b>Date:</b> {{ $waste->date }}</p>
                        <p><b>Total Bags:</b> {{ $waste->total_bags }}</p>
                        <p><b>Total Weight:</b> {{ $waste->total_weight }} kg</p>
                    </div>

                    <div class="col-md-8">

                        <h5 class="border-bottom pb-2">Waste Breakdown</h5>
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-2">
                                    <b>Infectious</b><br>
                                    {{ $waste->infections }} kg ({{ $waste->infections_bags ?? 0 }} bags)
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-2">
                                    <b>Pathological</b><br>
                                    {{ $waste->pathological }} kg ({{ $waste->pathological_bags ?? 0 }} bags)
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-2">
                                    <b>Chemical</b><br>
                                    {{ $waste->chemical }} kg ({{ $waste->chemical_bags ?? 0 }} bags)
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-2">
                                    <b>Pharmaceutical</b><br>
                                    {{ $waste->pharmaceutical }} kg ({{ $waste->pharmaceutical_bags ?? 0 }} bags)
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-2">
                                    <b>Sharps</b><br>
                                    {{ $waste->sharp }} kg ({{ $waste->sharp_bags ?? 0 }} bags)
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 border-bottom pb-2">Collected By</h5>
                        <p><b>Employee:</b> {{ $waste->employee->name ?? '-' }}</p>

                    </div>

                </div>

            </div>
        </div>
    </div>
</main>

<x-admin.footer />
