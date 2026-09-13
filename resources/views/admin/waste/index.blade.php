<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">All Wastes</h3>
            @if(auth()->user()->hasPermission('waste', 'insert'))
            <a href="{{route('waste.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Waste</a>
            @endif
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="wasteTable" style="min-width:1430px">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Company Name</th>
                                <th scope="col">Receipt No</th>
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
        $('#wasteTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('waste.ajax') }}",
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columnDefs: [
                { targets: 0, width: "126px" },
            ],

            columns: [
                { data: 'date' },
                { data: 'company_name' },
                { data: 'receipt_no' },
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