<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Employee</h3>
        </div>
        <!-- Table Element -->
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('employee.update', $edit->id) : route('employee.store') }}">
                    <div class="row row-cols-2">
                        <x-input-field 
                            type="text"
                            name="name" 
                            label="Name"
                            value="{{isset($edit) ? $edit->name : ''}}"
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="email" 
                            label="Email"
                            value="{{isset($edit) ? $edit->email : ''}}"
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="phone_no" 
                            label="Phone No" 
                            value="{{isset($edit) ? $edit->phone_no : ''}}"
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="cnic_no" 
                            label="CNIC No"
                            value="{{isset($edit) ? $edit->cnic_no : ''}}"
                        />

                        <div class="mb-3">
                            <label for="employee_type" class="form-label fw-semibold">Employee Type</label>
                            <select name="employee_type" id="employee_type" class="form-select">
                                <option value="Driver" {{ old('employee_type', $edit->employee_type ?? 'Driver') == '' ? 'selected' : '' }}>Driver</option>
                                <option value="Helper" {{ old('employee_type', $edit->employee_type ?? '') == 'Helper' ? 'selected' : '' }}>Helper</option>
                                <option value="Manager" {{ old('employee_type', $edit->employee_type ?? '') == 'Manager' ? 'selected' : '' }}>Manager</option>
                            </select>
                        </div>

                        <x-input-field 
                            type="number"
                            name="allowance" 
                            label="Allowance"
                            value="{{isset($edit) ? $edit->allowance : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="oldage_benefits" 
                            label="Oldage Benefits"
                            value="{{isset($edit) ? $edit->oldage_benefits : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="security_no" 
                            label="SS No"
                            value="{{isset($edit) ? $edit->security_no : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="license_no" 
                            label="License No"
                            value="{{isset($edit) ? $edit->license_no : ''}}"
                            divClass="license_no"
                        />
                         
                        <div class="mb-3">
                            <label for="city_id" class="form-label fw-semibold">City</label>
                            <select name="city_id" id="city_id" class="form-select choices">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" {{ (old('city_id', isset($edit) ? $edit->city_id : '') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label fw-semibold">Gender</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="male" {{ old('gender', $edit->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $edit->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $edit->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <x-input-field 
                            type="number"
                            name="salary" 
                            label="Salary"
                            value="{{isset($edit) ? $edit->salary : ''}}"
                        />

                        <div class="">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="active" {{ old('status', $edit->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $edit->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <x-input-field 
                            type="text"
                            name="bank_name" 
                            label="Bank Name"
                            value="{{isset($edit) ? $edit->bank_name : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="account_no" 
                            label="Account No"
                            value="{{isset($edit) ? $edit->account_no : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="iban" 
                            label="IBAN"
                            value="{{isset($edit) ? $edit->iban : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="education" 
                            label="Education"
                            value="{{isset($edit) ? $edit->education : ''}}"
                        />

                        <div class="col-12">
                            <x-text-area name="address" label="Address" value="{{ isset($edit) ? $edit->address : '' }}" />
                        </div>

                        <div id="reasonSection" class="col-12 {{ isset($edit) && $edit->status == 'inactive' ? 'd-block' : 'd-none' }}">
                            <x-text-area name="reason" label="Reason" value="{{ isset($edit) ? $edit->reason : '' }}" />
                        </div>
                        
                        <div class="d-flex gap-3 align-items-center mb-3 w-100">
                            <div class="ratio ratio-1x1" style="width: 130px;">
                                <img src="{{ 
                                    !empty(old('employee_image'))
                                        ? old('employee_image')
                                        : (isset($edit) && !empty($edit->employee_image)
                                            ? asset('public/'.$edit->employee_image)
                                            : asset('public/images/User.png'))
                                }}" alt="" data-img="{{ asset('public/images/User.png') }}" class="w-100 border rounded-circle object-fit-cover" id="previewImage">
                            </div>
                            <div class="w-100">
                                <label for="employee_image" class="border rounded px-3 py-3 w-100 d-block" style="cursor: pointer;"><i class="fa-solid fa-arrow-up-from-bracket"></i> Upload Photo</label>
                                <input type="file" class="d-none" id="employee_image" accept="image/*" name="employee_image">
                            </div>
                            
                        </div>
                        <span id="imageError" class="text-danger"></span>
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
    $(document).ready(function () {
        $('#employee_image').on('change', function () {
            const file = this.files[0];
            const maxSize = 5 * 1024 * 1024;
            $('#imageError').text('');
            if (!file) return;

            if (file.size > maxSize) {
                $(this).val('');
                $('#previewImage').attr('src', $('#previewImage').data('img'));
                $('#imageError').text('Image size must be less than 5 MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $('#previewImage').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });

        function license_no() {
            if ($("#employee_type").val() == "Driver") {
                $(".license_no").removeClass("d-none");
            }else {
                $(".license_no").addClass("d-none");
            }
        }
        license_no();
        $("#employee_type").on("change", function () {
            license_no();
        });

        $("#status").on("change", function(){
            var status = $(this).val();
            if (status == "inactive") {
                $("#reasonSection").removeClass("d-none");
                $("#reasonSection").addClass('d-block');
            }else {
                $("#reasonSection").addClass("d-none");
                $("#reasonSection").removeClass('d-block');

            }
        })
    })
</script>