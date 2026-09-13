<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Purchase Products</h3>
            @if(auth()->user()->hasPermission('product_purchase', 'insert'))
            <a href="{{route('purchase_product.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Purchase Product</a>
            @endif
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100" id="purchase_productTable">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width: 50px;"></th>
                                <th scope="col">Date</th>
                                <th scope="col">Total</th>
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

        var table = $('#purchase_productTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('purchase_product.ajax') }}",
            ordering: false,
            pageLength: 50,
            lengthMenu: [25, 50, 100],
            columns: [
                {
                    className: 'details-control',
                    orderable: false,
                    data: null,
                    defaultContent: '<i class="fa fa-plus-circle fa-2x" style="cursor:pointer;color:blue;"></i>'
                },
                { data: 'purchase_date' },
                { data: 'total' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        $('#purchase_productTable tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            var icon = $(this).find('i');

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                var purchases = row.data().purchases;

                var html = '<table class="table table-bordered mb-0">';
                html += '<thead><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>';

                purchases.forEach(function(p) {
                    html += '<tr>';
                    html += '<td>' + p.name + '</td>';
                    html += '<td>' + p.quantity + '</td>';
                    html += '<td>' + parseFloat(p.purchase_price).toFixed(2) + '</td>';
                    html += '<td>' + parseFloat(p.total).toFixed(2) + '</td>';
                    html += '</tr>';
                });

                html += '</tbody></table>';

                row.child(html).show();
                tr.addClass('shown');
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }
        });
    });
</script>