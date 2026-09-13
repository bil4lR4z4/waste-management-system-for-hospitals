<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            @if($page == 'setting')
                <h3 class="mb-0">Settings</h3>
                <a href="{{route('setting.pdf')}}" class="btn btn-success">Download PDF</a>
            @elseif($page == 'general_setting')
                <h3 class="mb-0">General Settings</h3>
            @endif
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ route('setting.update') }}">
                    <div class="row row-cols-1">

                        @if($page == 'setting')
                            <!-- <x-input-field 
                                type="text"
                                name="site_name" 
                                label="Site Name"
                                value="{{isset($setting) ? $setting->site_name : ''}}"
                            /> -->

                            <x-input-field 
                                type="text"
                                name="hospital_name" 
                                label="Company Name"
                                value="{{isset($setting) ? $setting->hospital_name : ''}}"
                            />

                            <div class="mb-3 ">
                                <label for="logo" class="form-label fw-semibold ">Site Logo</label>
                                <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                @if (isset($setting) && $setting->logo)
                                    <img src="{{asset($setting->logo ?? '' )}}" alt="" width="100px" class="mt-3">
                                @endif
                            </div>

                            <x-input-field 
                                type="text"
                                name="phone" 
                                label="Phone"
                                value="{{isset($setting) ? $setting->phone : ''}}"
                            />

                            <x-text-area name="address" label="Address" value="{{ isset($setting) ? $setting->address : '' }}" />

                            <x-input-field 
                                type="text"
                                name="ntn" 
                                label="NTN"
                                value="{{isset($setting) ? $setting->ntn : ''}}"
                            />

                            <x-input-field 
                                type="text"
                                name="pra" 
                                label="PRA Number"
                                value="{{isset($setting) ? $setting->pra : ''}}"
                            />

                            <x-input-field 
                                type="text"
                                name="secp" 
                                label="SECP Number"
                                value="{{isset($setting) ? $setting->secp : ''}}"
                            />

                            <div class="">
                                <h5>Bank Detail</h5>
                                <x-input-field 
                                    type="text"
                                    name="bank_name" 
                                    label="Bank Name"
                                    value="{{isset($setting) ? $setting->bank_name : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="bank_account_no" 
                                    label="Bank Account No"
                                    value="{{isset($setting) ? $setting->bank_account_no : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="bank_account_holder" 
                                    label="Account Holder"
                                    value="{{isset($setting) ? $setting->bank_account_holder : ''}}"
                                />

                                <div class="mb-3 ">
                                    <label for="bank_qr_code" class="form-label fw-semibold ">QR Code</label>
                                    <input type="file" name="bank_qr_code" id="bank_qr_code" class="form-control" accept="image/*">
                                    @if (isset($setting) && $setting->bank_qr_code)
                                        <img src="{{asset($setting->bank_qr_code ?? '' )}}" alt="" width="100px" class="mt-3">
                                    @endif
                                </div>
                            </div>

                            <div class="">
                                <h5>Mobile Payment Details</h5>
                                <x-input-field 
                                    type="text"
                                    name="phone_account_name" 
                                    label="Account Type"
                                    value="{{isset($setting) ? $setting->phone_account_name : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="phone_account_no" 
                                    label="Account No"
                                    value="{{isset($setting) ? $setting->phone_account_no : ''}}"
                                />

                                <x-input-field 
                                    type="text"
                                    name="phone_account_holder" 
                                    label="Account Holder"
                                    value="{{isset($setting) ? $setting->phone_account_holder : ''}}"
                                />

                                <div class="mb-3 ">
                                    <label for="phone_qr_code" class="form-label fw-semibold ">QR Code</label>
                                    <input type="file" name="phone_qr_code" id="phone_qr_code" class="form-control" accept="image/*">
                                    @if (isset($setting) && $setting->phone_qr_code)
                                        <img src="{{asset($setting->phone_qr_code ?? '' )}}" alt="" width="100px" class="mt-3">
                                    @endif
                                </div>
                            </div>
    
                        @elseif($page == 'general_setting')
                            <x-input-field 
                                type="text"
                                name="tax" 
                                label="Tax"
                                value="{{isset($setting) ? $setting->tax : ''}}"
                            />

                            <x-input-field 
                                type="text"
                                name="salary_limit" 
                                label="Salary Limit"
                                value="{{isset($setting) ? $setting->salary_limit : ''}}"
                            />
                    
                        @endif

                        <input type="hidden" value="{{$page ?? ''}}" name="page">
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
    flatpickr("#date", {
        dateFormat: "d-m-Y"
    });

    $(document).ready(function() {
        $('#hospital_id').on('change', function() {
            var selectedValue = $("#hospital_id option:selected").data('address');
            $('#address').val(selectedValue);
        });
    })
</script>

