<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\HospitalAgreementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\WasteDisposalsController;
use App\Http\Controllers\WasteBurnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\RentController;


Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('route:cache');

    return back()->with('success', 'Application cache cleared!');
})->name('clear.cache');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('signup');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/profile/{id}', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update/{id}', [UserController::class, 'updateProfile'])
            ->name('update.profile');

    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:employee,view');

        Route::get('/create', [EmployeeController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:employee,insert');

        Route::post('/store', [EmployeeController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:employee,insert');

        Route::get('/ajax', [EmployeeController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:employee,view');

        Route::get('/edit/{id}', [EmployeeController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:employee,edit');

        Route::post('/update/{id}', [EmployeeController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:employee,edit');

        Route::get('/destroy/{id}', [EmployeeController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:employee,delete');

        Route::get('/view/{id}', [EmployeeController::class, 'view'])
            ->name('view');

        Route::get('/pdf/{id}', [EmployeeController::class, 'pdf'])
            ->name('pdf');
    });

    Route::prefix('expense-type')->name('expense_type.')->group(function () {
        Route::get('/', [ExpenseTypeController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:expense_type,view');

        Route::post('/store', [ExpenseTypeController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:expense_type,insert');

        Route::get('/edit/{id}', [ExpenseTypeController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:expense_type,edit');

        Route::post('/update/{id}', [ExpenseTypeController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:expense_type,edit');

        Route::get('/destroy/{id}', [ExpenseTypeController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:expense_type,delete');
    });

    Route::prefix('expense')->name('expense.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:expense,view');

        Route::get('/ajax', [ExpenseController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:expense,view');

        Route::post('/store', [ExpenseController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:expense,insert');

        Route::get('/edit/{id}', [ExpenseController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:expense,edit');

        Route::post('/update/{id}', [ExpenseController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:expense,edit');

        Route::get('/destroy/{id}', [ExpenseController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:expense,delete');
    });

    Route::prefix('salary')->name('salary.')->group(function () {
        Route::get('/', [SalaryController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:salary,view');

        Route::get('/ajax', [SalaryController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:salary,view');

        Route::get('/create', [SalaryController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:salary,insert');

        Route::get('/get-employees', [SalaryController::class, 'getEmployees'])
            ->name('get-employees');

        Route::post('/store', [SalaryController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:salary,insert');

        Route::get('/edit/{id}', [SalaryController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:salary,edit');

        Route::post('/update/{id}', [SalaryController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:salary,edit');

        Route::post('/edit', [SalaryController::class, 'multiple_edit'])
            ->name('edit.multiple')
            ->middleware('check_permission:salary,edit');

        Route::post('/multiple-update', [SalaryController::class, 'multiple_update'])
            ->name('update.multiple')
            ->middleware('check_permission:salary,edit');

        Route::get('/destroy/{id}', [SalaryController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:salary,delete');
    });

    Route::prefix('company')->name('hospital.')->group(function () {
        Route::get('/', [HospitalController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:company,view');

        Route::get('/ajax', [HospitalController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:company,view');

        Route::get('/create', [HospitalController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:company,insert');

        Route::post('/store', [HospitalController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:company,insert');

        Route::get('/edit/{id}', [HospitalController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:company,edit');

        Route::post('/update/{id}', [HospitalController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:company,edit');

        Route::get('/destroy/{id}', [HospitalController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:company,delete');

        Route::get('/view/{id}', [HospitalController::class, 'view'])
            ->name('view')
            ->middleware('check_permission:company,view');

        Route::get('/pdf/{id}', [HospitalController::class, 'pdf'])
            ->name('pdf');
    });

    Route::prefix('hospital-agreement')->name('hospital_agreement.')->group(function () {
        Route::get('/', [HospitalAgreementController::class, 'index'])->name('index');
        Route::get('/ajax', [HospitalAgreementController::class, 'ajax'])->name('ajax');
        Route::get('/create', [HospitalAgreementController::class, 'create'])->name('create');
        Route::post('/store', [HospitalAgreementController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [HospitalAgreementController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [HospitalAgreementController::class, 'update'])->name('update');
        Route::get('/destroy/{id}', [HospitalAgreementController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:invoice,view');

        Route::get('/ajax', [InvoiceController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:invoice,view');

        Route::get('/create', [InvoiceController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:invoice,insert');

        Route::post('/store', [InvoiceController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:invoice,insert');

        Route::get('/edit/{id}', [InvoiceController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:invoice,edit');

        Route::post('/update/{id}', [InvoiceController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:invoice,edit');

        Route::get('/destroy/{id}', [InvoiceController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:invoice,delete');

        Route::get('/view/{id}', [InvoiceController::class, 'view'])
            ->name('view')
            ->middleware('check_permission:invoice,view');

        Route::get('/print/{id}', [InvoiceController::class, 'print'])
            ->name('print');
    });

    Route::prefix('waste')->name('waste.')->group(function () {
        Route::get('/', [WasteController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:waste,view');

        Route::get('/ajax', [WasteController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:waste,view');

        Route::get('/create', [WasteController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:waste,insert');

        Route::post('/store', [WasteController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:waste,insert');

        Route::get('/edit/{id}', [WasteController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:waste,edit');

        Route::post('/update/{id}', [WasteController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:waste,edit');

        Route::get('/destroy/{id}', [WasteController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:waste,delete');

        Route::get('/view/{id}', [WasteController::class, 'view'])
            ->name('view')
            ->middleware('check_permission:waste,view');

        Route::get('/pdf/{id}', [WasteController::class, 'pdf'])
            ->name('pdf');
    });

    Route::prefix('waste_disposals')->name('waste-disposals.')->group(function () {
        Route::get('/', [WasteDisposalsController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:waste_disposals,view');

        Route::get('/ajax', [WasteDisposalsController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:waste_disposals,view');

        Route::get('/create', [WasteDisposalsController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:waste_disposals,insert');

        Route::post('/store', [WasteDisposalsController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:waste_disposals,insert');

        Route::get('/edit/{id}', [WasteDisposalsController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:waste_disposals,edit');

        Route::post('/update/{id}', [WasteDisposalsController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:waste_disposals,edit');

        Route::get('/destroy/{id}', [WasteDisposalsController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:waste_disposals,delete');
    });

    Route::prefix('waste_burn')->name('waste-burn.')->group(function () {
        Route::get('/', [WasteBurnController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:waste_burn,view');

        Route::get('/ajax', [WasteBurnController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:waste_burn,view');

        Route::get('/create', [WasteBurnController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:waste_burn,insert');

        Route::post('/store', [WasteBurnController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:waste_burn,insert');

        Route::get('/edit/{id}', [WasteBurnController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:waste_burn,edit');

        Route::post('/update/{id}', [WasteBurnController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:waste_burn,edit');

        Route::get('/destroy/{id}', [WasteBurnController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:waste_burn,delete');
    });

    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:product,view');

        Route::get('/ajax', [ProductController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:product,view');

        Route::get('/create', [ProductController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:product,insert');

        Route::post('/store', [ProductController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:product,insert');

        Route::get('/edit/{id}', [ProductController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:product,edit');

        Route::post('/update/{id}', [ProductController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:product,edit');

        Route::get('/destroy/{id}', [ProductController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:product,delete');
    });

    Route::prefix('purchase-product')->name('purchase_product.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:product_purchase,view');

        Route::get('/ajax', [PurchaseController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:product_purchase,view');

        Route::get('/create', [PurchaseController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:product_purchase,insert');

        Route::post('/store', [PurchaseController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:product_purchase,insert');

        Route::get('/edit/{id}', [PurchaseController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:product_purchase,edit');

        Route::post('/update/{id}', [PurchaseController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:product_purchase,edit');

        Route::get('/destroy/{id}', [PurchaseController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:product_purchase,delete');
    });

    Route::prefix('sale-product')->name('sale_product.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:product_sale,view');

        Route::get('/ajax', [SaleController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:product_sale,view');

        Route::get('/create', [SaleController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:product_sale,insert');

        Route::post('/store', [SaleController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:product_sale,insert');

        Route::get('/edit/{id}', [SaleController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:product_sale,edit');

        Route::post('/update/{id}', [SaleController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:product_sale,edit');

        Route::get('/destroy/{id}', [SaleController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:product_sale,delete');
    });

    Route::prefix('rent-product')->name('rent_product.')->group(function () {
        Route::get('/', [RentController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:product_rent,view');

        Route::get('/ajax', [RentController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:product_rent,view');

        Route::get('/create', [RentController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:product_rent,insert');

        Route::post('/store', [RentController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:product_rent,insert');

        Route::get('/edit/{id}', [RentController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:product_rent,edit');

        Route::post('/update/{id}', [RentController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:product_rent,edit');

        Route::get('/destroy/{id}', [RentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:product_rent,delete');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:users,view');

        Route::get('/ajax', [UserController::class, 'ajax'])
            ->name('ajax')
            ->middleware('check_permission:users,view');

        Route::get('/create', [UserController::class, 'create'])
            ->name('create')
            ->middleware('check_permission:users,insert');

        Route::post('/store', [UserController::class, 'store'])
            ->name('store')
            ->middleware('check_permission:users,insert');

        Route::get('/edit/{id}', [UserController::class, 'edit'])
            ->name('edit')
            ->middleware('check_permission:users,edit');

        Route::post('/update/{id}', [UserController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:users,edit');

        Route::get('/destroy/{id}', [UserController::class, 'destroy'])
            ->name('destroy')
            ->middleware('check_permission:users,delete');
    });

    Route::prefix('expense-report')->name('expense_report.')->group(function () {
        Route::get('/', [ReportController::class, 'expenseReport'])
            ->name('index')
            ->middleware('check_permission:expense_report,view');

        Route::get('/ajax', [ReportController::class, 'expenseReportajax'])
            ->name('ajax')
            ->middleware('check_permission:expense_report,view');  
            
        Route::get('/pdf', [ReportController::class, 'expenseReportPdf'])
            ->name('pdf'); 
    });

    Route::prefix('invoice-report')->name('invoice_report.')->group(function () {
        Route::get('/', [ReportController::class, 'invoiceReport'])
            ->name('index')
            ->middleware('check_permission:invoice_report,view');

        Route::get('/ajax', [ReportController::class, 'invoiceReportajax'])
            ->name('ajax')
            ->middleware('check_permission:invoice_report,view');
            
        Route::get('/pdf', [ReportController::class, 'invoiceReportPdf'])
            ->name('pdf'); 
    });

    Route::prefix('general-report')->name('general_report.')->group(function () {
        Route::get('/', [ReportController::class, 'generalReport'])
            ->name('index')
            ->middleware('check_permission:general_report,view');

        Route::get('/ajax', [ReportController::class, 'generalReportajax'])
            ->name('ajax')
            ->middleware('check_permission:general_report,view');  
            
        Route::get('/pdf', [ReportController::class, 'generalReportPdf'])
            ->name('pdf');
    });

    Route::prefix('waste-report')->name('waste_report.')->group(function () {
        Route::get('/', [ReportController::class, 'wasteReport'])
            ->name('index')
            ->middleware('check_permission:waste_report,view');

        Route::get('/ajax', [ReportController::class, 'wasteReportajax'])
            ->name('ajax')
            ->middleware('check_permission:waste_report,view'); 
            
        Route::get('/pdf', [ReportController::class, 'wasteReportPdf'])
            ->name('pdf');
    });

    Route::prefix('disposal-waste-report')->name('disposal_waste_report.')->group(function () {
        Route::get('/', [ReportController::class, 'disposalWasteReport'])
            ->name('index')
            ->middleware('check_permission:waste_disposal_report,view');

        Route::get('/ajax', [ReportController::class, 'disposalWasteReportajax'])
            ->name('ajax')
            ->middleware('check_permission:waste_disposal_report,view'); 
            
        Route::get('/pdf', [ReportController::class, 'disposalWasteReportPdf'])
            ->name('pdf');
    });

    Route::prefix('burn-waste-report')->name('burn_waste_report.')->group(function () {
        Route::get('/', [ReportController::class, 'burnWasteReport'])
            ->name('index')
            ->middleware('check_permission:burn_waste_report,view');

        Route::get('/ajax', [ReportController::class, 'burnWasteReportajax'])
            ->name('ajax')
            ->middleware('check_permission:burn_waste_report,view'); 
            
        Route::get('/pdf', [ReportController::class, 'burnWasteReportPdf'])
            ->name('pdf');
    });

    Route::prefix('salary-report')->name('salary_report.')->group(function () {
        Route::get('/', [ReportController::class, 'salaryReport'])
            ->name('index')
            ->middleware('check_permission:salary_report,view');

        Route::get('/ajax', [ReportController::class, 'salaryReportajax'])
            ->name('ajax')
            ->middleware('check_permission:salary_report,view'); 

        Route::get('/pdf', [ReportController::class, 'salaryReportPdf'])
            ->name('pdf');
    });

    Route::prefix('sale-report')->name('sale_report.')->group(function () {
        Route::get('/', [ReportController::class, 'saleReport'])
            ->name('index')
            ->middleware('check_permission:sale_report,view');

        Route::get('/ajax', [ReportController::class, 'saleReportajax'])
            ->name('ajax')
            ->middleware('check_permission:sale_report,view'); 

        Route::get('/pdf', [ReportController::class, 'saleReportPdf'])
            ->name('pdf');
    });

    Route::prefix('purchase-report')->name('purchase_report.')->group(function () {
        Route::get('/', [ReportController::class, 'purchaseReport'])
            ->name('index')
            ->middleware('check_permission:purchase_report,view');

        Route::get('/ajax', [ReportController::class, 'purchaseReportajax'])
            ->name('ajax')
            ->middleware('check_permission:purchase_report,view'); 

        Route::get('/pdf', [ReportController::class, 'purchaseReportPdf'])
            ->name('pdf');
    });

    Route::prefix('rent-report')->name('rent_report.')->group(function () {
        Route::get('/', [ReportController::class, 'rentReport'])
            ->name('index')
            ->middleware('check_permission:rent_report,view');

        Route::get('/ajax', [ReportController::class, 'rentReportajax'])
            ->name('ajax')
            ->middleware('check_permission:rent_report,view'); 

        Route::get('/pdf', [ReportController::class, 'rentReportPdf'])
            ->name('pdf');
    });

    Route::prefix('setting')->name('setting.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])
            ->name('index')
            ->middleware('check_permission:setting,view');

        Route::post('/update', [SettingController::class, 'update'])
            ->name('update')
            ->middleware('check_permission:setting,update');

        Route::get('setting-pdf', [SettingController::class, 'settingPdf'])->name('pdf');
    });

    Route::get('general-setting', [SettingController::class, 'generalSetting'])
            ->name('general_setting.index')
            ->middleware('check_permission:general_setting,view');
});

