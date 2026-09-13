<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Expenses</h3>
        </div>
        <!-- Table Element -->
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                @if(auth()->user()->hasPermission('expense', 'insert') || isset($edit))
                <div class="mb-3">
                    <h5 class="fw-bold">{{isset($edit) ? 'Edit' : 'Add'}} Expense</h5>
                    <x-form method="POST" action="{{ isset($edit) ? route('expense.update', $edit->id) : route('expense.store') }}">
                        <div class="row row-cols-2">
                            <div class="mb-3">
                                <label for="name" class="form-label">Select Expense Type</label>
                                <select name="expense_type_id" id="expense_type_id" class="form-select choices">
                                    <option value="">Select Expense Type</option>
                                    @foreach($expense_types as $expense_type)
                                        <option value="{{$expense_type->id}}" {{isset($edit) && $edit->expense_type_id == $expense_type->id ? 'selected' : ''}}>{{$expense_type->name}} ({{$expense_type->short_name ?? 'Pk'}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input-field 
                                type="text"
                                name="amount" 
                                label="Amount"
                                value="{{isset($edit) ? $edit->amount : ''}}"
                            />
                        </div>
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </x-form>
                </div>
                <hr>
                @endif
                
                <div class="table-responsive">
                    <table class="table w-100" id="expenseTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Name</th>
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
        $('#expenseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('expense.ajax') }}",
            ordering: false,
            columns: [
                { data: 'expense_name' },
                { data: 'amount' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>