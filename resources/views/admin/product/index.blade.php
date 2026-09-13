<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Products</h3>
            @if(auth()->user()->hasPermission('product', 'insert'))
            <a href="{{route('product.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Product</a>
            @endif
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="productTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Product Name</th>
                                <!-- <th scope="col">Quantity</th> -->
                                <th scope="col">Price</th>
                                <th scope="col">Product Mode</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                    
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<x-admin.footer />
<script>
    $(document).ready(function () {
        $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('product.ajax') }}",
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'name' },
                // { data: 'quantity' },
                { data: 'price' },
                { data: 'product_mode' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>