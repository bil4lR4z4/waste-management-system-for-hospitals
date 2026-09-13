<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Product</h3>
        </div>
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('product.update', $edit->id) : route('product.store') }}">
                    <div class="row row-cols-2">
                        <x-input-field 
                            type="text"
                            name="name" 
                            label="Product Name"
                            value="{{isset($edit) ? $edit->name : ''}}"
                        />

                        <x-input-field 
                            type="text"
                            name="quantity" 
                            label="Quantity"
                            value="{{isset($edit) ? $edit->quantity : 1}}"
                            divClass="d-none"
                        />

                        <div class="mb-3">
                            <label for="product_mode" class="form-label fw-semibold">Select Product Mode</label>
                            <select name="product_mode" id="product_mode" class="form-select choices">
                                <option value="sale" {{ old('product_mode', $edit->product_mode ?? '') == 'sale' ? 'selected' : '' }}>Sale</option>
                                <option value="rent" {{ old('product_mode', $edit->product_mode ?? '') == 'rent' ? 'selected' : '' }}>Rent</option>
                            </select>
                            @error('product_mode')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        
                        <x-input-field 
                            type="text"
                            name="price" 
                            label="Price" 
                            value="{{isset($edit) ? $edit->price : ''}}"
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    flatpickr("#date", {
        dateFormat: "d-m-Y"
    });

    document.addEventListener('DOMContentLoaded', function () {
        const hospitalSelect = document.getElementById('hospital_id');
        const amountInput = document.getElementById('amount');

        hospitalSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const payment = selectedOption.getAttribute('data-payment') || '';
            amountInput.value = payment;
        });
    });
</script>

