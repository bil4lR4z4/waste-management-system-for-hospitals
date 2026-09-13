<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Hospital Invoices</h3>
            @if(auth()->user()->hasPermission('invoice', 'insert'))
            <a href="{{route('invoice.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Invoice</a>
            @endif
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="invoiceTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Company Name</th>
                                <th scope="col">Employee Name</th>
                                <th scope="col">Receipt No</th>
                                <th scope="col">Amount</th>
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
        $('#invoiceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('invoice.ajax') }}",
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'date' },
                { data: 'company_name' },
                { data: 'employee_name' },
                { data: 'receipt_no' },
                { data: 'amount' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>