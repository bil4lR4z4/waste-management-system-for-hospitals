@php
use App\Models\Setting;
$setting = Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('css/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">


</head>

<body class="bg-light">
    <div class="container-fluid bg-brown">
        <div class="row row-cols-1">
            <div class="d-flex justify-content-between align-items-center" style="box-shadow: 0px 1px 0px #bfbfbf;">
                <div class="sidebar-logo">
                    <a href="{{route('dashboard')}}"><img src="{{asset($setting->logo ?? '' )}}" alt="" style="width: 100px"></a>
                </div>
                <div class="d-flex">
                    <nav class="px-3 py-1">
                        <div class="navbar-collapse ">
                            <ul class="navbar-nav flex-row gap-2">
                                <li class="nav-item">
                                    <a href="{{route('clear.cache')}}" class="btn btn-light">Clear Cache</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                        <img src="{{asset('images/User.png')}}" class="avatar img-fluid rounded" alt="">
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{route('profile', Auth::user()->id)}}" class="dropdown-item">Profile</a>
                                        <a href="{{route('logout')}}" class="dropdown-item">Logout</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <!-- Content For Sidebar -->
            <div class="h-100">
                <ul class="sidebar-nav">
                   
                    <li class="sidebar-item">
                        <a href="{{route('dashboard')}}" class="sidebar-link {{ (Request::is('admin/dashboard') || Request::is('admin')) ? 'active' : ''}}">
                            <i class="fa-solid fa-chart-pie me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    @if (auth()->user()->hasPermission('employee', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('employee.index')}}" class="sidebar-link {{ (Request::is('admin/employee') || Request::is('admin/employee/create') || Request::is('admin/employee/edit/*')) ? 'active' : ''}}">
                                <i class="fa-solid fa-users me-2"></i>
                                Employees
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasPermission('salary', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('salary.index')}}" class="sidebar-link {{(Request::is('admin/salary') || Request::is('admin/salary/create') || Request::is('admin/salary/edit/*')) ? 'active' : ''}}">
                                <i class="fa-solid fa-dollar-sign me-2"></i>
                                Salaries
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasPermission('company', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('hospital.index')}}" class="sidebar-link {{(Request::is('admin/company') || Request::is('admin/company/create') || Request::is('admin/company/edit/*')) ? 'active' : ''}}">
                                <i class="fa-solid fa-building me-2"></i>
                                Companies
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasPermission('waste', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('waste.index')}}" class="sidebar-link {{(Request::is('admin/waste') || Request::is('admin/waste/create') || Request::is('admin/waste/edit/*')) ? 'active' : ''}}">
                                <i class="fa-solid fa-recycle me-2"></i>
                                Wastes
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasPermission('waste_disposals', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('waste-disposals.index')}}" class="sidebar-link {{(Request::is('admin/waste_disposals') || Request::is('admin/waste_disposals/create') || Request::is('admin/waste_disposals/edit/*')) ? 'active' : ''}}">
                            <i class="fa-regular fa-trash-can me-2"></i>
                                Wastes Disposal
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasPermission('waste_burn', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('waste-burn.index')}}" class="sidebar-link {{(Request::is('admin/waste_burn') || Request::is('admin/waste_burn/create') || Request::is('admin/waste_burn/edit/*')) ? 'active' : ''}}">
                                <i class="fa-solid fa-fire-burner me-2"></i>
                                Wastes Burn
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasPermission('invoice', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('invoice.index')}}" class="sidebar-link {{(Request::is('admin/invoice') || Request::is('admin/invoice/create') || Request::is('admin/invoice/edit/*')) ? 'active' : ''}}">
                                <i class="fa-solid fa-receipt me-2"></i>
                                Invoices
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasPermission('product', 'view') ||
                        auth()->user()->hasPermission('product_sale', 'view') || 
                        auth()->user()->hasPermission('product_purchase', 'view') || 
                        auth()->user()->hasPermission('product_rent', 'view'))
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-target="#products" data-bs-toggle="collapse"
                                aria-expanded="false">
                                <i class="fa-solid fa-boxes-stacked me-2"></i>
                                Product
                            </a>
                            <ul id="products" class="sidebar-dropdown list-unstyled collapse {{(
                            Request::is('admin/product') || 
                            Request::is('admin/product/create') ||
                            Request::is('admin/product/edit/*') || 
                            Request::is('admin/purchase-product') ||
                            Request::is('admin/purchase-product/create') ||
                            Request::is('admin/purchase-product/edit/*') ||
                            Request::is('admin/sale-product') ||
                            Request::is('admin/sale-product/create') ||
                            Request::is('admin/sale-product/edit/*') ||
                            Request::is('admin/rent-product') ||
                            Request::is('admin/rent-product/create') ||
                            Request::is('admin/rent-product/edit/*')
                            ? 'show' : '')}}" data-bs-parent="#sidebar">

                                @if(auth()->user()->hasPermission('product_sale', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('sale_product.index')}}" class="sidebar-link {{(Request::is('admin/sale-product') || Request::is('admin/sale-product/create') || Request::is('admin/sale-product/edit/*')) ? 'active' : ''}}">
                                            Sale Product
                                        </a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('product_rent', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('rent_product.index')}}" class="sidebar-link {{(Request::is('admin/rent-product') || Request::is('admin/rent-product/create') || Request::is('admin/rent-product/edit/*')) ? 'active' : ''}}">
                                            Rent Product
                                        </a>
                                    </li>
                                @endif
                                
                                @if(auth()->user()->hasPermission('product_purchase', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('purchase_product.index')}}" class="sidebar-link {{(Request::is('admin/purchase-product') || Request::is('admin/purchase-product/create') || Request::is('admin/purchase-product/edit/*')) ? 'active' : ''}}">
                                            Purchase Product
                                        </a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('product', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('product.index')}}" class="sidebar-link {{(Request::is('admin/product') || Request::is('admin/product/create') || Request::is('admin/product/edit/*')) ? 'active' : ''}}">
                                            Product
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    @endif

                    @if(auth()->user()->hasPermission('expense', 'view') || auth()->user()->hasPermission('expense_type', 'view'))
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-target="#expenses" data-bs-toggle="collapse"
                                aria-expanded="false"><i class="fa-solid fa-money-bill-wave me-2"></i>
                                Expenses
                            </a>
                            <ul id="expenses" class="sidebar-dropdown list-unstyled collapse {{(Request::is('admin/expense') || Request::is('admin/expense-type')  || Request::is('admin/expense-type/edit/*') || Request::is('admin/expense/edit/*') ? 'show' : '')}}" data-bs-parent="#sidebar">

                                @if(auth()->user()->hasPermission('expense_type', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('expense_type.index')}}" class="sidebar-link {{(Request::is('admin/expense-type') || Request::is('admin/expense-type/edit/*')) ? 'active' : ''}}">Expenses Type</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('expense', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('expense.index')}}" class="sidebar-link {{(Request::is('admin/expense') || Request::is('admin/expense/edit/*')) ? 'active' : ''}}">Expenses</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(
                        auth()->user()->hasPermission('expense_report', 'view') || 
                        auth()->user()->hasPermission('invoice_report', 'view') || 
                        auth()->user()->hasPermission('general_report', 'view') || 
                        auth()->user()->hasPermission('purchase_report', 'view') || 
                        auth()->user()->hasPermission('rent_report', 'view') || 
                        auth()->user()->hasPermission('sale_report', 'view') || 
                        auth()->user()->hasPermission('waste_report', 'view') || 
                        auth()->user()->hasPermission('salary_report', 'view') || 
                        auth()->user()->hasPermission('disposal_waste_report', 'view') || 
                        auth()->user()->hasPermission('burn_waste_report', 'view')
                        )
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse"
                                aria-expanded="false"><i class="fa-solid fa-file-invoice-dollar me-2"></i></i>
                                Reports
                            </a>
                            <ul id="reports" class="sidebar-dropdown list-unstyled collapse {{(Request::is('admin/expense-report') || Request::is('admin/invoice-report') || Request::is('admin/sale-report') ||Request::is('admin/purchase-report') || Request::is('admin/rent-report') || Request::is('admin/general-report') || Request::is('admin/waste-report') || Request::is('admin/salary-report') || Request::is('admin/disposal-waste-report') || Request::is('admin/burn-waste-report') ? 'show' : '')}}" data-bs-parent="#sidebar">
                                @if(auth()->user()->hasPermission('expense_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('expense_report.index')}}" class="sidebar-link {{Request::is('admin/expense-report') ? 'active' : ''}}">Expense Report</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('invoice_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('invoice_report.index')}}" class="sidebar-link {{Request::is('admin/invoice-report') ? 'active' : ''}}">Invoice Report</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('waste_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('waste_report.index')}}" class="sidebar-link {{Request::is('admin/waste-report') ? 'active' : ''}}">Waste Report</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('disposal_waste_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('disposal_waste_report.index')}}" class="sidebar-link {{Request::is('admin/disposal-waste-report') ? 'active' : ''}}">Disposal Waste Report</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('burn_waste_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('burn_waste_report.index')}}" class="sidebar-link {{Request::is('admin/burn-waste-report') ? 'active' : ''}}">Burn Waste Report</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('salary_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('salary_report.index')}}" class="sidebar-link {{Request::is('admin/salary-report') ? 'active' : ''}}">Salary Report</a>
                                    </li>
                                @endif
                                
                                @if(auth()->user()->hasPermission('sale_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('sale_report.index')}}" class="sidebar-link {{Request::is('admin/sale-report') ? 'active' : ''}}">Sale Report</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('purchase_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('purchase_report.index')}}" class="sidebar-link {{Request::is('admin/purchase-report') ? 'active' : ''}}">Purchase Report</a>
                                    </li>
                                @endif

                                @if(auth()->user()->hasPermission('rent_report', 'view'))
                                    <li class="sidebar-item">
                                        <a href="{{route('rent_report.index')}}" class="sidebar-link {{Request::is('admin/rent-report') ? 'active' : ''}}">Rent Report</a>
                                    </li>
                                @endif

                                <li class="sidebar-item">
                                    <a href="{{route('general_report.index')}}" class="sidebar-link {{Request::is('admin/general-report') ? 'active' : ''}}">General Report</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if (auth()->user()->hasPermission('users', 'view'))
                        <li class="sidebar-item">
                            <a href="{{route('users.index')}}" class="sidebar-link {{(Request::is('admin/users') || Request::is('admin/users/create') || Request::is('admin/users/edit/*')) ? 'active' : ''}}">
                                <i class="fa-solid fa-users me-2"></i>
                                Users
                            </a>
                        </li>                           
                    @endif
                    @if(auth()->user()->role == 'Admin')

                        <li class="sidebar-item">
                            <a href="{{route('setting.index')}}" class="sidebar-link {{Request::is('admin/setting') ? 'active' : ''}}">
                                <i class="fa-solid fa-gear me-2"></i>
                                Settings
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="{{route('general_setting.index')}}" class="sidebar-link {{Request::is('admin/general-setting') ? 'active' : ''}}">
                                <i class="fa-solid fa-gears me-2"></i>
                                General Settings
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand px-3">
                <button class="btn d-md-none d-block" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </nav>