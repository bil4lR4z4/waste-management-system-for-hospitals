<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Company</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('hospital.update', $edit->id) : route('hospital.store') }}">
                    <h5>General Informations</h5>
                    <div class="row row-cols-2">
                        <x-input-field 
                            type="text"
                            name="company_name" 
                            label="Company Name *"
                            value="{{isset($edit) ? $edit->company_name : ''}}"
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="owner_name"
                            label="Owner Name *"  
                            value="{{isset($edit) ? $edit->owner_name : ''}}"
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="email"
                            label="Email *"  
                            value="{{isset($edit) ? $edit->email : ''}}"
                            divClass="col-4"
                        />
                        
                        <x-input-field 
                            type="text"
                            name="registration_number" 
                            label="Registration Number *" 
                            value="{{isset($edit) ? $edit->registration_number : $newReg}}"
                            readonly
                        />

                        <x-input-field 
                            type="text"
                            name="ntn" 
                            label="NTN Number" 
                            value="{{isset($edit) ? $edit->ntn : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="phc" 
                            label="PHC Number" 
                            value="{{isset($edit) ? $edit->phc : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="pra" 
                            label="PRA Number" 
                            value="{{isset($edit) ? $edit->pra : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="secp" 
                            label="SECP Number" 
                            value="{{isset($edit) ? $edit->secp : ''}}"
                            divClass="d-none"
                        />

                        <div class="mb-3 col-12">
                            <label for="facilities" class="form-label fw-semibold">Select Facilities</label>
                            <select name="facilities[]" id="facilities" class="form-select choices" multiple>
                                <option value="">Select Facilities</option>
                                <option value="hospital" {{ in_array('hospital', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Hospital</option>
                                <option value="laboratory" {{ in_array('laboratory', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Laboratory</option>
                                <option value="Blood Bank" {{ in_array('Blood Bank', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Blood Bank</option>
                                <option value="Diagnostic Center" {{ in_array('Diagnostic Center', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Diagnostic Center</option>
                                <option value="Radiology Center" {{ in_array('Radiology Center', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Radiology Center</option>

                                <option value="Clinic" {{ in_array('Clinic', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Clinic</option>

                                <option value="Polyclinic" {{ in_array('Polyclinic', explode(',', $edit->facilities ?? '')) ? 'selected' : '' }}>Polyclinic</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <x-text-area name="address" label="Address*" value="{{ isset($edit) ? $edit->address : '' }}" />
                        </div>

                        <div class="mb-3 col-6">
                            <label for="city_id" class="form-label fw-semibold">Select City *</label>
                            <select name="city_id" id="city_id" class="form-select choices">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" {{ (old('city_id', isset($edit) ? $edit->city_id : '') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <span class="text-danger">City is required</span>
                            @enderror
                        </div>

                        <x-input-field 
                            type="text"
                            name="contact_number" 
                            label="Contact Number *" 
                            value="{{isset($edit) ? $edit->contact_number : ''}}"
                            divClass="col-6"
                        />

                        <x-input-field 
                            type="text"
                            name="date" 
                            label="Registration Date" 
                            value="{{ isset($edit) ? \Carbon\Carbon::parse($edit->date)->format('d-m-Y') : now()->format('d-m-Y') }}"
                            divClass="col-6"
                        />

                        <x-input-field 
                            type="text"
                            name="expiry_date" 
                            label="Expiry Date" 
                            value="{{ isset($edit) ? \Carbon\Carbon::parse($edit->expiry_date)->format('d-m-Y') : now()->format('d-m-Y') }}"
                            class="date"
                            divClass="col-6"
                        />

                        <x-input-field 
                            type="text"
                            name="payment" 
                            label="Payment" 
                            value="{{ isset($edit) ? $edit->payment : '' }}"
                            divClass="col-6"
                        />

                        <h5 class="w-100 mt-3">Facility Details</h5>

                        <x-input-field 
                            type="text"
                            name="number_of_beds" 
                            label="Number Of Beds" 
                            value="{{isset($edit) ? $edit->number_of_beds : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="waste_generation_volume" 
                            label="Waste Generation Volume (Approx. kg / day)" 
                            value="{{isset($edit) ? $edit->waste_generation_volume : ''}}"
                        />

                        <div class="col-12 mb-3">
                            <label for="" class="form-label fw-semibold">Type of Waste Generated</label>
                            <div class="form-check mb-2">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="infectious_waste" 
                                    value="Infectious Waste" 
                                    name="waste_type[]"
                                    {{ in_array(
                                        'Infectious Waste',
                                        old('waste_type', explode(',', $edit->waste_type ?? ''))
                                    ) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="infectious_waste">
                                    Infectious Waste
                                </label>
                            </div>


                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="pathological_waste" value="Pathological Waste" name="waste_type[]" {{ in_array('Pathological Waste', old('waste_type', explode(',', $edit->waste_type ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="pathological_waste">
                                    Pathological Waste
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="sharp" value="Sharp" name="waste_type[]" {{ in_array('Sharp', old('waste_type', explode(',', $edit->waste_type ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sharp">
                                    Sharps
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="chemical_waste" value="Chemical Waste" name="waste_type[]" {{ in_array('Chemical Waste', old('waste_type', explode(',', $edit->waste_type ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="chemical_waste">
                                    Chemical Waste
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pharmaceutical_waste" value="Pharmaceutical Waste" name="waste_type[]" {{ in_array('Pharmaceutical Waste', old('waste_type', explode(',', $edit->waste_type ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="pharmaceutical_waste">
                                    Pharmaceutical Waste
                                </label>
                            </div>

                        </div>
                        <h5 class="w-100 mt-3">Waste Collection Information</h5>
                        <div class="col-12 mb-3">
                            <label for="" class="form-label fw-semibold">Preferred Waste Collection Frequency</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" id="daily" value="Daily" name="preferred_waste_collection_frequency" {{ (old('preferred_waste_collection_frequency', isset($edit) ? $edit->preferred_waste_collection_frequency : '') == 'Daily') ? 'checked' : '' }}>
                                <label class="form-check-label" for="daily">
                                    Daily
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" id="weekly" value="Weekly" name="preferred_waste_collection_frequency" {{ (old('preferred_waste_collection_frequency', isset($edit) ? $edit->preferred_waste_collection_frequency : '') == 'Weekly') ? 'checked' : '' }}>
                                <label class="form-check-label" for="weekly">
                                    Weekly
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" id="weeklytotime" value="weekly 2 time" name="preferred_waste_collection_frequency" {{ (old('preferred_waste_collection_frequency', isset($edit) ? $edit->preferred_waste_collection_frequency : '') == 'weekly 2 time') ? 'checked' : '' }}>
                                <label class="form-check-label" for="weeklytotime">
                                    Weekly 2 Time
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" id="monthly" value="Monthly" name="preferred_waste_collection_frequency" {{ (old('preferred_waste_collection_frequency', isset($edit) ? $edit->preferred_waste_collection_frequency : '') == 'Monthly') ? 'checked' : '' }}>
                                <label class="form-check-label" for="monthly">
                                    Monthly
                                </label>
                            </div>

                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="has_temporary_waste_storage" value="yes" name="has_temporary_waste_storage" {{ (old('has_temporary_waste_storage', isset($edit) ? $edit->has_temporary_waste_storage : '') == 'yes') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_temporary_waste_storage">
                                    Does the facility have proper temporary Waste Storage
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="" class="form-label fw-semibold"> Waste Storage Method</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="bags" value="Bags" name="waste_storage_method[]"
                                {{ in_array('Bags', old('waste_storage_method', explode(',', $edit->waste_storage_method ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bags">
                                    Bags
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="bins" value="Bins" name="waste_storage_method[]"
                                {{ in_array('Bins', old('waste_storage_method', explode(',', $edit->waste_storage_method ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bins">
                                    Bins
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="containers" value="Containers" name="waste_storage_method[]" {{ in_array('Containers', old('waste_storage_method', explode(',', $edit->waste_storage_method ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="containers">
                                    Containers
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="yellow_room" value="Yellow Room" name="waste_storage_method[]" {{ in_array('Yellow Room', old('waste_storage_method', explode(',', $edit->waste_storage_method ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="yellow_room">
                                    Yellow Room
                                </label>
                            </div>

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

<x-admin.footer />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    flatpickr("#date", {
        dateFormat: "d-m-Y"
    });
</script>