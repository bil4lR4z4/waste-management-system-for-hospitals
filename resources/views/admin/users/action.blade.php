<x-admin.header title="IP Seller Admin" />
<style>
    .permission-cell {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .permission-cell:hover {
        background-color: #f0f8ff; 
        cursor: pointer;
    }
</style>

</style>
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} User</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('users.update', $edit->id) : route('users.store') }}">
                    <div class="row row-cols-2">
                        <x-input-field 
                            type="text"
                            name="first_name" 
                            label="First Name"
                            value="{{isset($edit) ? $edit->first_name : ''}}"
                            required
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="last_name" 
                            label="Last Name"
                            value="{{isset($edit) ? $edit->last_name : ''}}"
                            required
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="email"
                            name="email" 
                            label="Email"
                            value="{{isset($edit) ? $edit->email : ''}}"
                            required
                            divClass="col-4"
                        />

                        <x-input-field 
                            type="text"
                            name="phone" 
                            label="Phone No"
                            value="{{isset($edit) ? $edit->phone : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="ntn" 
                            label="NTN"
                            value="{{isset($edit) ? $edit->ntn : ''}}"
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
                        />

                        <x-input-field 
                            type="password"
                            name="password" 
                            label="Password"
                            value=""
                        />

                        @php
                            $selectedCities = [];

                            if(old('city_id')){
                                $selectedCities = old('city_id');
                            }
                        
                            elseif(isset($edit) && !empty($edit->city_ids)){
                                $selectedCities = explode(',', $edit->city_ids);
                            }
                        @endphp

                        <div class="mb-3">
                            <label for="city_id" class="form-label fw-semibold">Select Cities</label>
                            <select name="city_id[]" id="city_id" class="form-select choices" multiple>
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ in_array($city->id, $selectedCities) ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                                @error('city_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>select city</strong>
                                    </span>
                                @enderror
                            </select>
                        </div>

                        <div class="col-12">
                            <x-text-area name="address" label="Address" value="{{ isset($edit) ? $edit->address : '' }}" />
                        </div>
                        <div class="col-12">
                            <h5>Bank Details</h5>
                            <div class="row row-cols-2">
                                <x-input-field 
                                    type="text"
                                    name="bank_name" 
                                    label="Bank Name"
                                    value="{{isset($edit) ? $edit->bank_name : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="bank_account_no" 
                                    label="Bank Account No"
                                    value="{{isset($edit) ? $edit->bank_account_no : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="bank_account_holder" 
                                    label="Account Holder"
                                    value="{{isset($edit) ? $edit->bank_account_holder : ''}}"
                                />

                                <div class="mb-3 ">
                                    <label for="bank_qr_code" class="form-label fw-semibold ">QR Code</label>
                                    <input type="file" name="bank_qr_code" id="bank_qr_code" class="form-control" accept="image/*">
                                    @if (isset($edit) && $edit->bank_qr_code)
                                        <img src="{{asset($edit->bank_qr_code ?? '' )}}" alt="" width="100px" class="mt-3">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h5>Mobile Payment Details</h5>
                            <div class="row row-cols-2">
                                <x-input-field 
                                    type="text"
                                    name="phone_account_name" 
                                    label="Account Type"
                                    value="{{isset($edit) ? $edit->phone_account_name : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="phone_account_no" 
                                    label="Account No"
                                    value="{{isset($edit) ? $edit->phone_account_no : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="phone_account_holder" 
                                    label="Account Holder"
                                    value="{{isset($edit) ? $edit->phone_account_holder : ''}}"
                                />

                                <div class="mb-3 ">
                                    <label for="phone_qr_code" class="form-label fw-semibold ">QR Code</label>
                                    <input type="file" name="phone_qr_code" id="phone_qr_code" class="form-control" accept="image/*">
                                    @if (isset($edit) && $edit->phone_qr_code)
                                        <img src="{{asset($edit->phone_qr_code ?? '' )}}" alt="" width="100px" class="mt-3">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 w-100">
                            <h5>Assign Permissions</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-start">Page / Module</th>
                                            <th>View</th>
                                            <th>Insert</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle">
                                        @php
                                            $modules = [
                                                'employee',
                                                'expense_type',
                                                'expense',
                                                'salary',
                                                'company',
                                                'invoice',
                                                'product',
                                                'product_purchase',
                                                'product_sale',
                                                'product_rent',
                                                'waste',
                                                'waste_disposals',
                                                'waste_burn',
                                                'users',
                                                'expense_report',
                                                'sale_report',
                                                'purchase_report',
                                                'rent_report',
                                                'invoice_report',
                                                'waste_report',
                                                'burn_waste_report',
                                                'waste_disposal_report',
                                                'salary_report',
                                                'general_report'
                                            ];

                                            $labels = [
                                                'employee' => 'Employee',
                                                'expense_type' => 'Expense Type',
                                                'expense' => 'Expense',
                                                'salary' => 'Salary',
                                                'company' => 'Company',
                                                'invoice' => 'Invoice',
                                                'product' => 'Product',
                                                'product_purchase' => 'Product Purchase',
                                                'product_sale' => 'Product Sale',
                                                'product_rent' => 'Product Rent',
                                                'waste' => 'Waste',
                                                'waste_disposals' => 'Waste Disposals',
                                                'waste_burn' => 'Waste Burns',
                                                'users' => 'Users',
                                                'expense_report' => 'Expense Report',
                                                'sale_report' => 'Sale Report',
                                                'purchase_report' => 'Purchase Report',
                                                'rent_report' => 'Rent Report',
                                                'invoice_report' => 'Invoice Report',
                                                'waste_report' => 'Waste Report',
                                                'burn_waste_report' => 'Waste Burn Report',
                                                'waste_disposal_report' => 'Waste Disposal Report',
                                                'salary_report' => 'Salary Report',
                                                'general_report' => 'General Report',
                                            ];

                                            $reportModules = ['expense_report', 'sale_report','purchase_report','rent_report','invoice_report','waste_report', 'burn_waste_report','waste_disposal_report','salary_report','general_report'];
                                       
                                            $userPermissions = [];
                                            if(isset($edit)){
                                                foreach($modules as $module){
                                                    $perm = $edit->permissions()->where('page', $module)->first();
                                                    $userPermissions[$module] = $perm ? $perm->id : null;
                                                }
                                            }
                                        @endphp

                                        @foreach ($modules as $module)
                                        <tr>
                                            <td class="permission-cell text-start text-capitalize">{{ $labels[$module] }}</td>

                                            <td class="permission-cell">
                                                <input type="checkbox" class="form-check-input" name="permissions[{{ $module }}][view]" value="1" 
                                                {{
                                                    old("permissions.$module.view") 
                                                    ? 'checked' 
                                                    : (isset($edit) && $edit->hasPermission($module,'view') ? 'checked' : '')
                                                }}>

                                                 <input type="hidden" name="permissions[{{ $module }}][id]" value="{{ $userPermissions[$module] ?? '' }}">
                                            </td>

                                            <td class="permission-cell">
                                                <input type="checkbox" class="form-check-input" name="permissions[{{ $module }}][insert]" value="1" 
                                                {{ in_array($module, $reportModules) ? 'disabled' : '' }}
                                                {{
                                                    old("permissions.$module.insert") 
                                                    ? 'checked' 
                                                    : (isset($edit) && $edit->hasPermission($module,'insert') ? 'checked' : '')
                                                }}>
                                            </td>

                                            <td class="permission-cell">
                                                <input type="checkbox" class="form-check-input" name="permissions[{{ $module }}][edit]" value="1" 
                                                {{ in_array($module, $reportModules) ? 'disabled' : '' }}

                                                {{
                                                    old("permissions.$module.edit") 
                                                    ? 'checked' 
                                                    : (isset($edit) && $edit->hasPermission($module,'edit') ? 'checked' : '')
                                                }}>
                                            </td>

                                            <td class="permission-cell">
                                                <input type="checkbox" class="form-check-input" name="permissions[{{ $module }}][delete]" value="1" 
                                                {{ in_array($module, $reportModules) ? 'disabled' : '' }}
                                                {{
                                                    old("permissions.$module.delete") 
                                                    ? 'checked' 
                                                    : (isset($edit) && $edit->hasPermission($module,'delete') ? 'checked' : '')
                                                }}>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

<script>
$(document).ready(function() {
    $('.permission-cell').on('click', function(e) {
        if (!$(e.target).is('input[type="checkbox"]')) {
            let checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));
        }
    });

    $(document).on('click', '.text-capitalize', function () {
        var row = $(this).closest('tr');
        var checkbox = row.find('input[type="checkbox"]');

        checkbox.prop('checked', !checkbox.prop('checked'));
    });

});
</script>
