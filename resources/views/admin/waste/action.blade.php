<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Waste</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('waste.update', $edit->id) : route('waste.store') }}">
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
                            value="{{ isset($edit) ? $edit->receipt_no : '' }}"
                        />

                        <div class="mb-3">
                            <label for="city_id" class="form-label fw-semibold">Select City</label>
                            <select name="city_id" id="city_id" class="form-select select2">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    @php
                                        $hospitalData = $city->hospitals->map(function($h){
                                            return ['id' => $h->id, 'text' => $h->company_name];
                                        });

                                        $employeeData = $city->employees->map(function($e){
                                            return ['id' => $e->id, 'text' => $e->name];
                                        })
                                    @endphp
                                    <option value="{{ $city->id }}"  data-hospitals='@json($hospitalData)' data-employees='@json($employeeData)' {{ (old('city_id', isset($edit) ? $edit->city_id : '') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('hospital_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="hospital_id" class="form-label fw-semibold">Select Company</label>
                            <select name="hospital_id" id="hospital_id" class="form-select select2">
                                <option value="">Select Company</option>
                                @foreach ($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}" data-address="{{ $hospital->address }}" {{ (old('hospital_id', isset($edit) ? $edit->hospital_id : '') == $hospital->id) ? 'selected' : '' }}>{{ $hospital->company_name }}</option>
                                @endforeach
                            </select>
                            @error('hospital_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="employee_id" class="form-label fw-semibold">Select Employee</label>
                            <select name="employee_id" id="employee_id" class="form-select select2">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ (old('employee', isset($edit) ? $edit->employee_id : '') == $employee->id) ? 'selected' : '' }}>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="w-100"></div>

                        <x-input-field 
                            type="number"
                            name="infections" 
                            label="Infections(KG)" 
                            value="{{isset($edit) ? $edit->infections : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="infections_bags" 
                            label="Infections Bags" 
                            value="{{isset($edit) ? $edit->infections_bags : ''}}"
                        />
                        
                        <x-input-field 
                            type="number"
                            name="sharp" 
                            label="Sharp(KG)" 
                            value="{{isset($edit) ? $edit->sharp : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="sharp_bags" 
                            label="Sharp Bags" 
                            value="{{isset($edit) ? $edit->sharp_bags : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="pathological" 
                            label="Pathological(KG)" 
                            value="{{isset($edit) ? $edit->pathological : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="pathological_bags" 
                            label="Pathological Bags" 
                            value="{{isset($edit) ? $edit->pathological_bags : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="chemical" 
                            label="Chemical(KG)" 
                            value="{{isset($edit) ? $edit->chemical : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="chemical_bags" 
                            label="Chemical Bags" 
                            value="{{isset($edit) ? $edit->chemical_bags : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="pharmaceutical" 
                            label="Pharmaceutical(KG)" 
                            value="{{isset($edit) ? $edit->pharmaceutical : ''}}"
                        />

                        <x-input-field 
                            type="number"
                            name="pharmaceutical_bags" 
                            label="Pharmaceutical Bags" 
                            value="{{isset($edit) ? $edit->pharmaceutical_bags : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="total_weight" 
                            label="Total Weight(KG)" 
                            value="{{isset($edit) ? $edit->total_weight : ''}}"
                            readonly=true
                        />

                        <x-input-field 
                            type="text"
                            name="total_bags" 
                            label="Total Bags" 
                            value="{{isset($edit) ? $edit->total_bags : ''}}"
                            readonly=true
                        />

                        <div class="col-12 d-none">
                            <x-text-area name="address" label="Address" value="{{ isset($edit) ? $edit->address : '' }}" />
                        </div>

                        <div class="w-100">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</main>
<!-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif -->

<x-admin.footer />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    flatpickr("#date", {
        dateFormat: "d-m-Y"
    });

    $(document).ready(function() {
        $('#hospital_id').on('change', function() {
            var selectedValue = $("#hospital_id option:selected").data('address');
            $('#address').val(selectedValue);
        });

        $(document).on('input',"#infections , #sharp, #pharmaceutical, #chemical, #pathological", function() {
            var infections = $("#infections").val() || 0;
            var sharp = $("#sharp").val() || 0;
            var pharmaceutical = $("#pharmaceutical").val() || 0;
            var chemical = $("#chemical").val() || 0;
            var pathological = $("#pathological").val() || 0;
            var total_weight = parseFloat(infections) + parseFloat(sharp) + parseFloat(pharmaceutical) + parseFloat(chemical) + parseFloat(pathological);
            console.log(total_weight);
            $("#total_weight").val(total_weight);
        });

        $(document).on('input',"#infections_bags , #sharp_bags, #pharmaceutical_bags, #chemical_bags, #pathological_bags", function() {
            var infections_bags = $("#infections_bags").val() || 0;
            var sharp_bags = $("#sharp_bags").val() || 0;
            var pathological_bags = $("#pathological_bags").val() || 0;
            var chemical_bags = $("#chemical_bags").val() || 0;
            var pharmaceutical_bags = $("#pharmaceutical_bags").val() || 0;
            var total_bags = parseFloat(infections_bags) + parseFloat(sharp_bags) + parseFloat(pharmaceutical_bags) + parseFloat(chemical_bags) + parseFloat(pathological_bags);
            
            $("#total_bags").val(total_bags);
        });

        @if(old('hospital_id'))
            $hospital.val('{{ old("hospital_id") }}').trigger('change');
        @elseif(isset($edit))
            $hospital.val('{{ $edit->hospital_id }}').trigger('change');
        @endif

        @if(old('employee_id'))
            $('#employee_id').val({{ json_encode(old("employee_id")) }}).trigger('change');
        @elseif(isset($edit))
            $('#employee_id').val({{ json_encode([$edit->employee_id]) }}).trigger('change');
        @endif
        $('#city_id').on('change', function() {
            let hospitals = $(this).find(':selected').data('hospitals');
            let employees = $(this).find(':selected').data('employees');

            let $employeeSelect = $('#employee_id');
            let $hospitalSelect = $('#hospital_id');

            $employeeSelect.empty().append('<option value="">Select Employee</option>');
            $hospitalSelect.empty().append('<option value="">Select Company</option>');

            if(hospitals && hospitals.length > 0){
                hospitals.forEach(function(h){
                    $hospitalSelect.append(new Option(h.text, h.id));
                });
            }

            if(employees && employees.length > 0){
                employees.forEach(function(e){
                    $employeeSelect.append(new Option(e.text, e.id));
                });
            }

            $hospitalSelect.trigger('change.select2');
            $employeeSelect.trigger('change.select2');
        });

        
    })
</script>

