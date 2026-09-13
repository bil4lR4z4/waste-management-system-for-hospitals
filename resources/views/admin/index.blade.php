<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Dashboard</h4>
        </div>
        <div class="row row-cols-3">
            <div class="">
                <div class="card border-info bg-info-subtle">
                    <div class="card-body text-info-emphasis">
                        <h3 class="mb-1">{{$totalEmployees ?? 0}}</h3>
                        Total Employees
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card border-warning bg-primary-warning">
                    <div class="card-body text-warning-emphasis">
                        <h3 class="mb-1">{{$totalExpenses ?? 0}}</h3>
                        Total Expenses This Month
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card border-danger bg-danger-subtle">
                    <div class="card-body text-danger-emphasis">
                        <h3 class="mb-1">{{$totalIncomes ?? 0}}</h3>
                        Total Income
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>
<x-admin.footer />