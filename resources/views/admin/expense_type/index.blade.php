<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Expense Type</h3>
        </div>
        <!-- Table Element -->
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                @if(auth()->user()->hasPermission('expense_type', 'insert') || isset($edit))
                <div class="mb-3">
                    <h5 class="fw-bold">{{isset($edit) ? 'Edit' : 'Add'}} Expense Type</h5>
                    <x-form method="POST" action="{{ isset($edit) ? route('expense_type.update', $edit->id) : route('expense_type.store') }}">
                        <div class="row row-cols-2">
                            <x-input-field 
                                type="text"
                                name="name" 
                                label="Name"
                                value="{{isset($edit) ? $edit->name : ''}}"
                            />

                            <div class="mb-3">
                                <label for="city_id" class="form-label fw-semibold">Select City</label>
                                <select name="city_id" id="city_id" class="form-select choices">
                                    <option value="0">Pakistan</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" {{ (old('city_id', isset($edit) ? $edit->city_id : '') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
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
                                <th scope="col">City</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense_types as $expense_type)
                                <tr>
                                    <td>{{$expense_type->name}}</td>
                                    <td>{{$expense_type->city_name ?? 'Pakistan'}}</td>
                                    <td>
                                        @if(auth()->user()->hasPermission('expense_type', 'edit'))
                                            <a href="{{route('expense_type.edit', $expense_type->id)}}" class="btn btn-sm btn-primary">Edit</a>
                                        @endif
                                        @if(auth()->user()->hasPermission('expense_type', 'delete'))
                                            <button class="btn btn-danger btn-sm delete" data-action="{{route('expense_type.destroy', $expense_type->id)}}" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
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
            ordering: false,
            paging: true,
            searching: true
        });
    });
</script>