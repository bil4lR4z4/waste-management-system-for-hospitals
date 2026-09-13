<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Invoice</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('invoice.update', $edit->id) : route('invoice.store') }}">
                    <div class="row row-cols-2">
                        <x-input-field 
                            type="text"
                            name="date" 
                            label="Date"
                            value="{{ isset($edit) ? \Carbon\Carbon::parse($edit->date)->format('d-m-Y') : now()->format('d-m-Y') }}"
                        />

                        <x-input-field 
                            type="text"
                            name="receipt_no" 
                            label="Receipt No"
                            value="{{isset($edit) ? $edit->receipt_no : $reciept_no}}"
                            readonly=true
                        />

                        <div class="mb-3">
                            <label for="hospital_id" class="form-label fw-semibold">Select Company</label>
                            <select name="hospital_id" id="hospital_id" class="form-select select2">
                                <option value="">Select Company</option>
                                @foreach ($hospitals as $hospital)
                                <option value="{{ $hospital->id }}" data-payment="{{ $hospital->payment }}" {{ (old('hospital_id', isset($edit) ? $edit->hospital_id : '') == $hospital->id) ? 'selected' : '' }}>{{ $hospital->company_name }} ({{ $hospital->short_name }})</option>
                                @endforeach
                            </select>
                            @error('hospital_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        
                        <x-input-field 
                            type="text"
                            name="amount" 
                            label="Amount" 
                            value="{{isset($edit) ? $edit->amount : ''}}"
                        />

                        <div class="mb-3">
                            <label for="employee_id" class="form-label fw-semibold">Select Employee</label>
                            <select name="employee_id" id="employee_id" class="form-select select2">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ (old('employee', isset($edit) ? $edit->employee_id : '') == $employee->id) ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->short_name }})</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="mb-3 w-100">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="tax_status" name="tax_status" {{ (old('tax_status', isset($edit) ? $edit->tax_status : '') == 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tax_status">
                                    Include Tax
                                </label>
                            </div>
                        </div>
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <input type="submit" name="print" class="btn btn-primary" value="Save & Print">
                        </div>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</main>

<x-admin.footer />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    flatpickr("#date", {
        dateFormat: "d-m-Y"
    });

    $(document).ready(function() {
        $('#hospital_id').on('change', function() {
           var selectedValue = $("#hospital_id option:selected").data('payment');
           $('#amount').val(selectedValue);
        });
    })
</script>

