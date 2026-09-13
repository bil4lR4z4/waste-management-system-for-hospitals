<x-admin.header title="IP Seller Admin" />
<style>
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
            <h3 class="mb-0">User Profile</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('update.profile', $edit->id) : route('users.store') }}">
                    <div class="row row-cols-2">
                        <x-input-field 
                            type="text"
                            name="first_name" 
                            label="First Name"
                            value="{{isset($edit) ? $edit->first_name : ''}}"
                            required
                        />

                        <x-input-field 
                            type="text"
                            name="last_name" 
                            label="Last Name"
                            value="{{isset($edit) ? $edit->last_name : ''}}"
                            required
                        />

                        <x-input-field 
                            type="email"
                            name="email" 
                            label="Email"
                            value="{{isset($edit) ? $edit->email : ''}}"
                            required
                        />
                        @if(auth()->user()->role != 'Admin' )

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
                        @endif
                        <x-input-field 
                            type="password"
                            name="old_password" 
                            label="Old Password"
                            value=""
                        />

                        <x-input-field 
                            type="password"
                            name="new_password" 
                            label="New Password"
                            value=""
                        />

                        <x-input-field 
                            type="password"
                            name="password_confirmation" 
                            label="Confirm Password"
                            value=""
                        />
                        @if(auth()->user()->role != 'Admin' )
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
                        @endif
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
</script>

<script>
$(document).ready(function() {
    // Jab user cell par click kare
    $('.permission-cell').on('click', function(e) {
        // Agar checkbox khud click hua ho to double toggle na ho
        if (!$(e.target).is('input[type="checkbox"]')) {
            let checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));
        }
    });
});
</script>
