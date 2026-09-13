<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Salary</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('salary.update', $edit->id) : route('salary.store') }}">
                    <div class="row row-cols-2">
                        @if (!isset($edit))
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input shadow-none" type="checkbox" value="" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Sellect All
                                </label>
                            </div>
                        </div>
                      
                        <div class="mb-3">
                            <label for="city_id" class="form-label fw-semibold">Select City</label>
                            <select name="city_id" id="city_id" class="form-select select2">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" {{ (old('city_id') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label for="employee_id" class="form-label fw-semibold">Select Employee</label>
                            <select name="{{ isset($edit) ? 'employee_id' : 'employee_id[]' }}" id="employee_id" class="form-select" {{ isset($edit) ? '' : 'multiple' }}>
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-salary="{{ $employee->salary }}" data-allowance="{{ $employee->allowance }}"  {{ (old('employee_id', isset($edit) ? $edit->employee_id : '') == $employee->id) ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->short_name }})</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <span class="text-danger">Employee is required</span>
                            @enderror
                        </div>

                        <x-input-field 
                            type="text"
                            name="date" 
                            label="Date"
                            value="{{ isset($edit) ? \Carbon\Carbon::parse($edit->date)->format('d-m-Y') : now()->format('d-m-Y') }}"
                        />

                        <x-input-field 
                            type="text"
                            name="amount" 
                            label="Salary Amount"
                            value="{{isset($edit) ? $edit->amount : ''}}"
                            divClass="amountSection"
                        />

                        <x-input-field 
                            type="text"
                            name="allowance"
                            label="Allowance"  
                            value="{{isset($edit) ? $edit->allowance : ''}}"
                            divClass="allowanceSection"
                        />

                        <x-input-field 
                            type="text"
                            name="bonus" 
                            label="Bonus" 
                            value="{{isset($edit) ? $edit->bonus : ''}}"
                        />

                        <div class="w-100">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</main>
<x-admin.footer />
<script>
    flatpickr("#date", {
        dateFormat: "d-m-Y"
    });
</script>
<script>
$(document).ready(function() {
    $('#employee_id').select2({
        placeholder: "Select Employee",
        allowClear: true,
        width: '100%'
    });

    $('#selectAll').on('change', function() {
        const options = $('#employee_id option').not('[value=""]');

        if(options.length === 0) {
            $(this).prop('checked', false);
            return;
        }

        if($(this).is(':checked')) {
            options.prop('selected', true);
            $('.amountSection, .allowanceSection').addClass('d-none');
        } else {
            options.prop('selected', false);
            $('.amountSection, .allowanceSection').removeClass('d-none');
        }

        $('#employee_id').trigger('change.select2');
    });

    $('#city_id').on('change', function() {
        $('#selectAll').prop('checked', false);
        $('.amountSection, .allowanceSection').removeClass('d-none');

        const cityId = $(this).val();
        // if(!cityId) return;

        $.ajax({
            url: "{{ route('salary.get-employees') }}",
            type: 'GET',
            data: { city_id: cityId },
            success: function(response) {
                $('#employee_id').select2('destroy');
                $('#employee_id').empty();
                $('#employee_id').append(response);

                $('#employee_id').select2({
                    placeholder: "Select Employee",
                    allowClear: true,
                    width: '100%'
                });
                $('#employee_id').trigger('change');
            }
        });
    });
});
</script>

