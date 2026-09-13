<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">All Users</h3>
            @if(auth()->user()->hasPermission('users', 'insert'))
            <a href="{{route('users.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add User</a>
            @endif
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="wasteTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
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
            ajax: "{{ route('users.ajax') }}",
            ordering: false,
            columns: [
                { data: 'name' },
                { data: 'email' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>