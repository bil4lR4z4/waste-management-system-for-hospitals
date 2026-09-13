<x-admin.header title="IP Seller Admin" />
<style>
    .choices__item.choices__item--choice {
        padding-right: 0px !important;
    }
</style>
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Purchase Report</h3>
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="row mb-3 align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold" for="from_date">From Date</label>
                        <input type="text" id="from_date" class="form-control form-control-lg date"
                        value="{{ now()->subMonth()->format('d-m-Y') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold" for="to_date">To Date</label>
                        <input type="text" id="to_date" class="form-control form-control-lg date" value="{{ now()->format('d-m-Y') }}">
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
                    <table class="table w-100" id="purchase_reportTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total Amount:</th>
                                <th id="totalAmount">0</th>
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

<script>
    $(document).ready(function () {
        let applyFilter = false;

        $('#exportPdf').on('click', function () {
            let url = `{{ route('purchase_report.pdf') }}`;

            if (applyFilter) {
                let from_date = $('#from_date').val();
                let to_date   = $('#to_date').val();

                url += `?from_date=${from_date}&to_date=${to_date}`;
            }
            window.open(url, '_blank');
        });

        let table = $('#purchase_reportTable').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: 'Export CSV',
                    title: 'Invoice Report',
                    className: 'btn btn-primary'
                }
            ],
            ajax: {
                url: "{{ route('purchase_report.ajax') }}",
                data: function (d) {
                    if (applyFilter) {
                        d.from_date = $('#from_date').val();
                        d.to_date   = $('#to_date').val();
                    }else{
                        d.from_date = '';
                        d.to_date   = '';
                    }
                },
                dataSrc: function (json) {
                    $('#totalAmount').text(json.totalAmount);
                    return json.data;
                }
            },
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'date' },
                { data: 'amount' },
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
            $('#hospital').val('');
            $('#from_date, #to_date').removeClass('border border-danger');
            window.location.reload();
        });
    });
</script>