<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">All Waste Disposals</h3>
            @if(auth()->user()->hasPermission('waste_disposals', 'insert'))
            <a href="{{route('waste-disposals.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Waste Disposal</a>
            @endif
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="wasteDisposalTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Vehicle No</th>
                                <th scope="col">City</th>
                                <th scope="col">Infections(Bags)</th>
                                <th scope="col">Sharp(Bags)</th>
                                <th scope="col">Chemical(Bags)</th>
                                <th scope="col">Pharmaceutical(Bags)</th>
                                <th scope="col">Pathological(Bags)</th>
                                <th scope="col">Total Weight(Bags)</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                    
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<x-admin.footer />
<script>
    $(document).ready(function () {
        $('#wasteDisposalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('waste-disposals.ajax') }}",
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columnDefs: [
                { targets: 0, width: "126px" },
            ],

            columns: [
                { data: 'date' },
                { data: 'vehicle_no' },
                { data: 'city_name' },
                { data: 'infections' },
                { data: 'sharp' },
                { data: 'chemical' },
                { data: 'pharmaceutical' },
                { data: 'pathological' },
                { data: 'total_weight' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>