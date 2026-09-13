<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Hospital Agreement</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('hospital.update', $edit->id) : route('hospital.store') }}">
                    <div class="row row-cols-2">
                        <div class="mb-3 col-4">
                            <label for="hospital_id" class="form-label fw-semibold">Select Hospital</label>
                            <select name="hospital_id" id="hospital_id" class="form-select choices">
                                <option value="">Select Hospital</option>
                                @foreach ($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}" {{ (old('hospital_id', isset($edit) ? $edit->hospital_id : '') == $hospital->id) ? 'selected' : '' }}>{{ $hospital->company_name }}</option>
                                @endforeach
                            </select>
                            @error('hospital_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <x-input-field 
                            type="text"
                            name="start_date" 
                            label="Start Date"
                            value="{{isset($edit) ? $edit->start_date : ''}}"
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="end_date" 
                            label="End Date"
                            value="{{isset($edit) ? $edit->end_date : ''}}"
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="number_of_beds" 
                            label="Number of Beds" 
                            value="{{isset($edit) ? $edit->number_of_beds : ''}}"
                            divClass="col-4"
                        />
                        
                        <x-input-field 
                            type="text"
                            name="registration_number" 
                            label="Registration Number" 
                            value="{{isset($edit) ? $edit->registration_number : ''}}"
                            divClass="col-4"
                        />

                        <div class="mb-3 col-12">
                            <label for="facilities" class="form-label fw-semibold">Select Facilities</label>
                            <select name="facilities[]" id="facilities" class="form-select choices" multiple>
                                <option value="">Select Facilities</option>
                                <option value="hospital" {{ in_array('hospital', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Hospital</option>
                                <option value="laboratory" {{ in_array('laboratory', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Laboratory</option>
                                <option value="blood_bank" {{ in_array('blood_bank', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Blood Bank</option>
                                <option value="diagnostic_center" {{ in_array('diagnostic_center', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Diagnostic Center</option>
                                <option value="radiology_center" {{ in_array('radiology_center', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Radiology Center</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <x-text-area name="address" label="Address" value="{{ isset($edit) ? $edit->address : '' }}" />
                        </div>

                        

                        <x-input-field 
                            type="text"
                            name="contact_number" 
                            label="Contact Number" 
                            value="{{isset($edit) ? $edit->contact_number : ''}}"
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
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<x-admin.footer />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    flatpickr("#start_date", {
        dateFormat: "d-m-Y"
    });

    flatpickr("#end_date", {
        dateFormat: "d-m-Y"
    });
</script>

