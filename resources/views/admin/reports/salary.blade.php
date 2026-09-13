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
            <h3 class="mb-0">Salary Report</h3>
        </div>
        
        <div class="card border-0 overflow-hidden">
            <div class="card-body">
                <div class="row mb-3 align-items-end">
                    <div class="col-md-3 mb-3 ps-4">
                        <div class="form-check">
                            <input class="form-check-input fs-5" type="checkbox" value="" id="includeTax">
                            <label class="form-check-label fs-5" for="includeTax">
                                Include Allowance
                            </label>
                        </div>
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
                    <table class="table w-100" id="waste_reportTable">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Employee Name</th>
                                <th scope="col">City</th>   
                                <th scope="col">Salary</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    $(document).ready(function () {
        $('#exportPdf').on('click', function () {
            let city      = $('#city').val();
            let includeTax = $('#includeTax').is(':checked') ? 1 : 0;
            let url = `{{ route('salary_report.pdf') }}?includeTax=${includeTax}`;

            if(city && city.length > 0){
                city.forEach(function(c){
                    url += `&city[]=${c}`;
                });
            }

            window.open(url, '_blank');
        });

        let table = $('#waste_reportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('salary_report.ajax') }}",
                data: function (d) {
                    d.city      = $('#city').val() ?? [];
                    d.includeTax = $('#includeTax').is(':checked') ? 1 : 0;
                },
            },
            ordering: false,
            pageLength: 50, 
            lengthMenu: [25, 50, 100],
            columns: [
                { data: 'name' },
                { data: 'city_name' },
                { data: 'salary' },
            ]
        });
        $('#filter').on('click', function () {
            table.ajax.reload();
        });
        $('#reset').on('click', function () {
            window.location.reload();
        });
    });
</script>