<x-admin.header title="IP Seller Admin" />
<style>
    .choices__item.choices__item--choice {
        padding-right: 0px !important;
    }
    .card{
        overflow: visible !important;
    }
</style>
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">General Report</h3>
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="row mb-3 align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold" for="from_date">From Date</label>
                        <input type="text" id="from_date" class="form-control form-control-lg date" value="{{ now()->subMonth()->format('d-m-Y') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold" for="to_date">To Date</label>
                        <input type="text" id="to_date" class="form-control form-control-lg date" value="{{ now()->format('d-m-Y') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold" for="city">City</label>
                        <select class="form-control choices" id="city" multiple>
                            <option value="">All</option>
                            @foreach ($cities as $city)
                                <option value="{{$city->id}}">{{$city->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold" for="payment_type">Payment Type</label>
                        <select class="form-control choices" id="payment_type" multiple>
                            <option value="">All</option>
                            <option value="salary">Salery</option>
                            <option value="invoice">Invoice</option>
                            @foreach ($expenses as $expense)
                                <option value="{{$expense->id}}">{{$expense->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-success btn-lg w-100" id="filter">Filter</button>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-secondary btn-lg w-100" id="reset">Reset</button>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-danger btn-lg w-100" id="exportPdf">Export PDF</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table w-100" id="general_reportTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Payment Type</th>
                                <th scope="col">Credit</th>
                                <th scope="col">Debit</th>
                                <th scope="col">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align:right">Total:</th>
                                <th id="totalCredit"></th>
                                <th id="totalDebit"></th>
                                <th></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<x-admin.footer />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    $(document).ready(function () {
        let applyFilter = false;

        $('#exportPdf').on('click', function () {
            let url = `{{ route('general_report.pdf') }}`;

            if (applyFilter) {
                let from_date = $('#from_date').val();
                let to_date   = $('#to_date').val();
                let city      = $('#city').val() ?? [];
                let payment_type   = $('#payment_type').val() ?? [];

                url += `?from_date=${from_date}&to_date=${to_date}&city=${city}&payment_type=${payment_type}`;
            }
            window.open(url, '_blank');
        });

        let table = $('#general_reportTable').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: 'Export CSV',
                    title: 'Expense Report',
                    className: 'btn btn-primary'
                }
            ],
            ajax: {
                url: "{{ route('general_report.ajax') }}",
                data: function (d) {
                    if(applyFilter){
                        d.from_date = $('#from_date').val();
                        d.to_date   = $('#to_date').val();
                        d.city      = $('#city').val() ?? [];
                        d.payment_type   = $('#payment_type').val() ?? [];
                    }else{
                        d.from_date = '';
                        d.to_date   = '';
                        d.city      = [];
                        d.payment_type   = [];
                    }
                    
                },
                dataSrc: function (json) {
                    $('#totalCredit').html(json.sumCredit);
                    $('#totalDebit').html(json.sumDebit);
                    return json.data;
                }
            },
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'date' },
                { data: 'payment_type' },
                { data: 'credit' },
                { data: 'debit' },
                { 
                    data: 'amount',
                    render: function(data, type, row){
                        let cls = row.amount_sign === 'positive' ? 'text-success' : 'text-danger';
                        return '<span class="'+cls+'">'+data+'</span>';
                    }
                }
            ]
        });

        $('#filter').on('click', function () {
            let from = $('#from_date').val();
            let to   = $('#to_date').val();

            $('#from_date, #to_date').removeClass('border border-danger');

            let hasError = false;
            if (from || to) {
                if (!from) {
                    $('#from_date').addClass('border border-danger');
                    hasError = true;
                }

                if (!to) {
                    $('#to_date').addClass('border border-danger');
                    hasError = true;
                }

                if (from && to) {
                    let fromDate = moment(from, 'DD-MM-YYYY');
                    let toDate   = moment(to, 'DD-MM-YYYY');

                    if (fromDate.isAfter(toDate)) {
                        $('#from_date, #to_date').addClass('border border-danger');
                        hasError = true;
                    }
                }
            }
            if (hasError) return; 
            applyFilter = true;
            table.ajax.reload();
        });

        $('#reset').on('click', function () {
            $('#from_date').val('');
            $('#to_date').val('');
            $('#city').val('');
            $('#payment_type').val('');
            $('#from_date, #to_date').removeClass('border border-danger');
            table.ajax.reload();
        });
    });
</script>