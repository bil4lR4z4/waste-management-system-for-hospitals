<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">All Employee</h3>
            @if(auth()->user()->hasPermission('employee', 'insert'))
                <a href="{{route('employee.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Employee</a>
            @endif
        </div>
        <!-- Table Element -->
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="employeeTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Info</th>
                                <th scope="col">Phone No</th>
                                <th scope="col">CNIC</th>
                                <th scope="col">Gender</th>
                                <th scope="col">Salary</th>
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
        $('#employeeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('employee.ajax') }}",
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'info' },
                { data: 'phone_no' },
                { data: 'cnic_no' },
                { data: 'gender' },
                { data: 'salary' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>