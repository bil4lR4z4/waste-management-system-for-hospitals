<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Sale Product</h3>
        </div>
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ isset($edit) ? route('sale_product.update', $edit->id) : route('sale_product.store') }}">
                    <div class="row row-cols-4">
                        <div class="mb-3">
                            <label for="" class="form-label fw-semibold">Date</label>
                            <input type="text" class="date form-control" value="{{isset($edit) ? \Carbon\Carbon::parse($edit->date)->format('d-m-Y') : now()->format('d-m-Y')}}" name="date">
                        </div>
                        <div class="hospitalDiv">
                            <label for="hospital_id" class="form-label fw-semibold">Select Company</label>
                            <select id="hospital_id" class="form-select select2" name="hospital_id">
                                <option value="">Select Company</option> 
                                @foreach ($hospitals as $hospital)
                                    <option value="{{$hospital->id}}" {{isset($edit) && $edit->hospital_id == $hospital->id ? 'selected' : ''}}>{{$hospital->company_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row row-cols-4 align-items-end mb-5">
                        <div class="productDiv">
                            <label for="product" class="form-label fw-semibold">Select Product</span></label>
                            <select id="product" class="form-select select2">
                                <option value="">Select Product</option> 
                                @foreach ($products as $product)
                                    <option value="{{$product->id}}" data-quantity="{{$product->quantity}}" data-price="{{$product->price}}" data-stock="{{$product->stock}}">{{$product->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="">
                            <label for="quantity" class="form-label fw-semibold">Quantity</label>
                            <input type="text" class="form-control onlyNumber" id="quantity">
                        </div>
                        <div class="">
                            <label for="price" class="form-label fw-semibold">Price</label>
                            <input type="text" class="form-control onlyNumber" id="price">
                        </div>
                        <div class="">
                            <button class="btn btn-primary" id="add"><i class="fa-solid fa-plus"></i>Add List</button>
                        </div>
                    </div>
               
                    <div class="">
                        <div class="table-responsive">
                            <table class="table w-100 table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($edit))
                                        @foreach ($edit->sales as $sale)
                                            <tr>
                                                <td>
                                                    <span class='text appendName'>{{$sale->product->name}}</span>
                                                    <input type="hidden" value="{{$sale->product_id}}" class="appendProductId" />
                                                    <input type="text" value="{{$sale->id}}" hidden name="sale[]">
                                                    <span class='input' hidden>
                                                        <select class="form-control select2 appendSelect" name="product_id[]">
                                                            @foreach ($products as $product)
                                                                <option value="{{$product->id}}" data-quantity="{{$product->quantity}}" data-price="{{$product->price}}" {{($product->id == $sale->product_id) ? 'selected' : ''}}>{{$product->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class='text appendQuantityText'>{{$sale->quantity}}</span>
                                                    <span class='input' hidden>
                                                        <input type="text" value="{{$sale->quantity}}" class="onlyNumber form-control appendQuantityInput" name="quantity[]" />
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class='text appendPriceText'>{{$sale->sale_price}}</span>
                                                    <span class='input' hidden><input type="text" value="{{$sale->sale_price}}" class="onlyNumber form-control appendPriceInput" name="price[]" /></span>
                                                </td>
                                                <td>
                                                    <span class='appendTotalText'>{{$sale->total}}</span>
                                                    <input type="text" value="{{$sale->total}}" class="onlyNumber form-control appendTotalInput" hidden name="total[]" />
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-success edit"><i class="fa-solid fa-pen"></i></button>
                                                    <button class="btn btn-sm btn-danger remove"><i class="fa-solid fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <x-text-area name="note" label="Note" value="{{isset($edit) ? $edit->note : ''}}" />
                        <div class="d-flex mb-3">
                            <div class="">
                                <label for="" class="form-label fw-semibold">Total</label>
                                <input type="text" class="form-control onlyNumber" id="total" name="grand_total" value="{{isset($edit) ? $edit->total : ''}}" readonly>
                            </div>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-primary">{{isset($edit) ? 'Update' : 'Save'}}</button>
                        </div>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</main>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<x-admin.footer />

<script>
    $(document).ready(function() {
        $(document).on('input', '.onlyNumber', function() {
            var value = $(this).val();
            value = value
                .replace(/[^0-9.]/g, '')
                .replace(/(\..*)\./g, '$1');

            $(this).val(value);
        })

        $("#product").on("change", function() {
            var quantity = $(this).find(':selected').data('quantity');
            var price = $(this).find(':selected').data('price');

            $("#quantity").val(1);
            $("#price").val(price);
        })

        function grandTotal() {
            var grandTotal = 0;
            $("tbody tr").each(function() {
                grandTotal += parseFloat($(this).find('.appendTotalInput').val());
            });
            
            $("#total").val(grandTotal.toFixed(2));
        }

        $("#add").on("click", function(e) {
            e.preventDefault();
            var productName = $("#product option:selected").text();
            var productId = $("#product option:selected").val();
            var quantity = parseFloat($("#quantity").val()) || 0;
            var price    = parseFloat($("#price").val()) || 0;

            let isValid = true;

            $("#quantity, #price").removeClass('is-invalid');
            $(".productDiv .select2-selection--single").css("borderColor", "#ced4da");

            if (!productId) {
                $(".productDiv .select2-selection--single").css("borderColor", "#dc3545");
                isValid = false;
            }

            if (!quantity || quantity <= 0) {
                $("#quantity").addClass('is-invalid');
                isValid = false;
            }

            if (!price || price <= 0) {
                $("#price").addClass('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                return false;
            }

            var total = quantity * price;

            var selectOptions = '';
            $("#product option").each(function() {
                if ($(this).val() !== "") {
                    var selected = $(this).val() == productId ? 'selected' : '';
                    selectOptions += `<option value="${$(this).val()}" data-quantity="${$(this).data('quantity')}" data-price="${$(this).data('price')}" ${selected}>${$(this).text()}</option>`;
                }
            });

            var html = `
                        <tr>
                            <td>
                                <span class='text appendName'>${productName}</span>
                                <input type="hidden" value="${productId}" class="appendProductId" />
                                <span class='input' hidden>
                                    <select class="form-control select2 appendSelect" name="product_id[]">${selectOptions}</select>
                                </span>
                            </td>
                            <td>
                                <span class='text appendQuantityText'>${quantity}</span>
                                <span class='input' hidden>
                                    <input type="text" value="${quantity}" class="onlyNumber form-control appendQuantityInput" name="quantity[]" />
                                </span>
                            </td>
                            <td>
                                <span class='text appendPriceText'>${price}</span>
                                <span class='input' hidden><input type="text" value="${price}" class="onlyNumber form-control appendPriceInput" name="price[]" /></span>
                            </td>
                            <td>
                                <span class='appendTotalText'>${total}</span>
                                <input type="text" value="${total}" class="onlyNumber form-control appendTotalInput" hidden name="total[]" />
                            </td>
                            <td>
                                <button class='btn btn-sm btn-success edit'><i class='fa-solid fa-pen'></i></button>
                                <button class='btn btn-sm btn-danger remove'><i class='fa-solid fa-trash'></i></button>
                            </td>
                        </tr>
            `;

            $("tbody").append(html);

            $("tbody tr:last select").select2();
            $("#product").val(null).trigger("change");
            $("#quantity").val('');
            $("#price").val('');

            grandTotal()
        })

        $(document).on('click', '.remove', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();

            grandTotal()
        })

        $(document).on('click', '.edit', function (e) {
            e.preventDefault();
            var btn = $(this);
            var row = btn.closest('tr');

            row.find('.text').prop('hidden', true);
            row.find('.input').prop('hidden', false);

            btn.removeClass('edit btn-success')
            .addClass('save btn-primary')
            .html('<i class="fa-solid fa-check"></i>');
        });

        $(document).on('click', '.save', function (e) {
            e.preventDefault();
            var btn = $(this);
            var row = btn.closest('tr');

            row.find('.text').prop('hidden', false);
            row.find('.input').prop('hidden', true);

            btn.removeClass('save btn-primary')
            .addClass('edit btn-success')
            .html('<i class="fa-solid fa-pen"></i>');
        });

        $(document).on("change", ".appendSelect",  function() {
            var price = $(this).find(':selected').data('price') || 0;
            var name = $(this).find(':selected').text();

            $(this).closest('tr').find('.appendName').text(name);
            $(this).closest('tr').find('.appendQuantityInput').val(1);
            $(this).closest('tr').find('.appendPriceInput').val(price);
            $(this).closest('tr').find('.appendQuantityText').text(1);
            $(this).closest('tr').find('.appendPriceText').text(price);
            $(this).closest('tr').find('.appendTotalText').text(price);
            $(this).closest('tr').find('.appendTotalInput').val(price);

            grandTotal();
        });

        function singleTotal(row) {
            var quantity = row.find('.appendQuantityInput').val();
            var price    = row.find('.appendPriceInput').val();
            var total    = quantity * price;

            row.find('.appendQuantityText').text(quantity);
            row.find('.appendPriceText').text(price);
            row.find('.appendTotalText').text(total);
            row.find('.appendTotalInput').val(total);
            grandTotal();
        }

        $(document).on('input', '.appendQuantityInput, .appendPriceInput', function() {
            var row = $(this).closest('tr');
            singleTotal(row);
        });

        $('form').on('submit', function (e) {
            let hospital_id = $('#hospital_id').val();
            let totalValue  = parseFloat($('#total').val()) || 0;

            $('#total').removeClass('is-invalid');
            $(".select2-selection--single").css("borderColor", "#ced4da");

            let hasError = false;
            if (!hospital_id || totalValue <= 0) {

                if (!hospital_id) {
                    $(".hospitalDiv .select2-selection--single")
                        .css("borderColor", "#dc3545");
                    hasError = true;
                }

                if (totalValue <= 0) {
                    $("#total").addClass('is-invalid');
                    hasError = true;
                }
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }
        });

    })
</script>

