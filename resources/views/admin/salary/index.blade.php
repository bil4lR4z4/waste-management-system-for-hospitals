<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">All Salaries</h3>
            <div class="d-flex gap-2">
                <!-- <form action="{{ route('salary.edit.multiple') }}" method="POST">
                    @csrf
                    <input type="text" name="salary_ids" id="salary_ids" hidden>
                    <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                </form> -->
                @if(auth()->user()->hasPermission('salary', 'insert'))
                <a href="{{route('salary.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Salary</a>
                @endif
            </div>
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="salaryTable">
                        <thead class="table-secondary">
                            <tr>
                                <!-- <th scope="col"><input type="checkbox" class="form-check-input shadow-none" id="selectAll"></th> -->
                                <th scope="col">Date</th>
                                <th scope="col">Employee Name</th>
                                <th scope="col">Salary Amount</th>
                                <th scope="col">Bonus</th>
                                <th scope="col">Allowance</th>
                                <th scope="col">Total</th>
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
        // $('#selectAll').on('click', function () {
        //     if (this.checked) {
        //         var ids = [];
        //         $('.checkbox').each(function () {
        //             this.checked = true;
        //             ids.push($(this).val());
        //         });
        //     } else {
        //         $('.checkbox').each(function () {
        //             this.checked = false;
        //             ids = [];
        //         });
        //     }
        //     $('#salary_ids').val(ids.join(','));
        // })

        // $(document).on('click', '.checkbox', function () {
        //     var ids = [];
        //     $('.checkbox').each(function () {
        //         if (this.checked) {
        //             ids.push($(this).val());
        //         }
        //     });
        //     $('#salary_ids').val(ids.join(','));
        // })

        $('#salaryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('salary.ajax') }}",
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'date' },
                { data: 'employee_name' },
                { data: 'amount' },
                { data: 'bonus' },
                { data: 'allowance' },
                { data: 'total' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>