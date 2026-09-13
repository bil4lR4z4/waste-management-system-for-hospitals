<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">All Companies</h3>
            @if(auth()->user()->hasPermission('company', 'insert'))
            <a href="{{route('hospital.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Company</a>
            @endif
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="salaryTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Company Name</th>
                                <th scope="col">Owner Name</th>
                                <th scope="col">Register No</th>
                                <th scope="col">Phone</th>
                                <th scope="col">City</th>
                                <th scope="col">Status</th>
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
        $('#salaryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('hospital.ajax') }}",
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'company_name' },
                { data: 'owner_name' },
                { data: 'registration_number' },
                { data: 'phone' },
                { data: 'city' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>