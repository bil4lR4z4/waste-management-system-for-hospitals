<?php

namespace App\Http\Controllers;
use App\Models\Waste;
use App\Models\WasteDisposal;
use App\Models\WasteBurn;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Salary;
use App\Models\Setting;
use App\Models\City;
use App\Models\Hospital;
use App\Models\TotalSale;
use App\Models\TotalPurchase;
use App\Models\TotalRent;
use App\Models\User;
use DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class ReportController extends Controller{

    public function expenseReport(){
        $user = auth()->user();
        $allowedCities = [];

        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();

        $expenses = ExpenseType::where('trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user) {

                if ($user->role === 'Manager') {
                    $q->where('owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('owner_id', $user->parent_id);
                }
            })
            ->get();

        return view('admin.reports.index', compact('cities', 'expenses'));
    }

    public function expenseReportajax(Request $request){
        $user = auth()->user();
        $search = $request->input('search.value');
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $city      = $request->city;
        $expense   = $request->expense;

        $expenses = Expense::join('expense_types', 'expense_types.id', '=', 'expenses.expense_type_id')
        ->leftJoin('cities', 'expense_types.city_id', '=', 'cities.id')
        ->select(
            DB::raw("'Expense' as expense_type"),
            'expense_types.id as expense_id',
            DB::raw("CONCAT(expense_types.name, ' (', COALESCE(cities.short_name, 'PK'), ')') as name"),
            'cities.id as city_id', 
            'cities.name as city_name',
            'expenses.amount as amount',
            'expenses.created_at as date'
        );
        if ($user->role !== 'Admin') {
            $expenses->where('expense_types.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $salaries = Salary::join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->select(
                DB::raw("'Salary' as expense_type"),
                DB::raw("0 as expense_id"), 
                DB::raw("CONCAT('Salary (', employees.name, ')') as name"),
                'cities.id as city_id',   
                'cities.name as city_name',
                DB::raw('( COALESCE(salaries.amount,0) + COALESCE(salaries.allowance,0) + COALESCE(salaries.bonus,0)) as amount'),
                'salaries.created_at as date'
            );

        if ($user->role !== 'Admin') {
            $salaries->where('employees.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $data = $expenses->unionAll($salaries);

        $query = DB::query()->fromSub($data, 't');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_type', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('amount', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('date', [$from, $to]);
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('city_id', $city);
        }

        if (!empty($expense) && is_array($expense)) {
            if (in_array('salary', $expense)) {
                $query->where(function($q) use ($expense) {
                    if (in_array('salary', $expense)) {
                        $q->orWhere('expense_type', 'Salary');
                    }
                    $expIds = array_filter($expense, fn($e) => $e !== 'salary');
                    if (!empty($expIds)) {
                        $q->orWhereIn('expense_id', $expIds);
                    }
                });
            } else {
                $query->whereIn('expense_id', $expense);
            }
        }


        $recordsTotal = DB::query()->fromSub($data, 't')->count();
        $recordsFiltered = $query->count();
        $results = $query
            ->orderBy('date', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        $totalAmount = $results->sum('amount');
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'totalAmount' => number_format($totalAmount),
            'data' => $results->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->date)->format('d-m-Y'),
                    'expense_type' => $row->name,
                    'amount' => number_format($row->amount),
                ];
            }),
        ]);
    }

    public function expenseReportPdf(){
        $user = auth()->user();
        $expenses = Expense::join('expense_types', 'expense_types.id', '=', 'expenses.expense_type_id')
            ->leftJoin('cities', 'expense_types.city_id', '=', 'cities.id')
            ->select(
                DB::raw("'Expense' as expense_type"),
                'expense_types.id as expense_id',
                DB::raw("CONCAT(expense_types.name, ' (', COALESCE(cities.short_name, 'PK'), ')') as name"),
                'cities.id as city_id', 
                'cities.name as city_name',
                'expenses.amount as amount',
                'expenses.created_at as date'
            );

        if ($user->role !== 'Admin') {
            $expenses->where('expense_types.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $salaries = Salary::join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->select(
                DB::raw("'Salary' as expense_type"),
                DB::raw("0 as expense_id"), 
                DB::raw("CONCAT('Salary (', employees.name, ')') as name"),
                'cities.id as city_id',   
                'cities.name as city_name',
                DB::raw('( COALESCE(salaries.amount,0) + COALESCE(salaries.allowance,0) + COALESCE(salaries.bonus,0)) as amount'),
                'salaries.created_at as date'
            );

        if ($user->role !== 'Admin') {
            $salaries->where('employees.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $data = $expenses->unionAll($salaries);
        $results = DB::query()
            ->fromSub($data, 't')
            ->orderBy('date', 'desc')
            ->get();

        $totalAmount = $results->sum('amount');

        $setting = Setting::first();
        $image = public_path($setting->logo);

        $pdf = Pdf::loadView('admin.reports.expense_pdf', compact('results','totalAmount', 'setting', 'image'));
        $pdf->setPaper('A4', 'portrait');

        // return $pdf->stream('expense-report.pdf');
        $filename = 'expense-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);

    }
    
    public function invoiceReport(){
        $user = auth()->user();
        $allowedCities = [];

        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();

        return view('admin.reports.invoice', compact('cities'));
    }

    public function invoiceReportajax(Request $request){
        $search = $request->input('search.value');
        $user   = auth()->user();
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $city      = $request->city;

        $query = Invoice::join('hospitals', 'hospitals.id', '=', 'invoices.hospital_id')
        ->leftjoin('cities', 'hospitals.city_id', '=', 'cities.id')
        ->select(
            'invoices.date',
            'invoices.receipt_no',
            'invoices.amount',
            'hospitals.company_name',
            'cities.name as city_name',
            'cities.id as city_id'
        );

        if ($user->role !== 'Admin') {
            $query->where('hospitals.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('invoices.date', 'like', "%{$search}%")
                ->orWhere('invoices.receipt_no', 'like', "%{$search}%")
                ->orWhere('invoices.amount', 'like', "%{$search}%")
                ->orWhere('hospitals.company_name', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('invoices.date', [$from, $to]);
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('hospitals.city_id', $city);
        }

        $recordsTotal = Invoice::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('invoices.date', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        
        $totalAmount = $results->sum('amount');
        

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'totalAmount' => number_format($totalAmount),
            'data' => $results->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->date)->format('d-m-Y'),
                    'receipt_no' => $row->receipt_no,
                    'company_name' => $row->company_name,
                    'amount' => number_format($row->amount),
                ];
            }),
        ]);
    }

    public function invoiceReportPdf(Request $request){
        $user = auth()->user();
        $query = Invoice::join('hospitals', 'hospitals.id', '=', 'invoices.hospital_id')
            ->leftJoin('cities', 'hospitals.city_id', '=', 'cities.id')
            ->select(
                'invoices.date',
                'invoices.receipt_no',
                'invoices.amount',
                'hospitals.company_name',
                'cities.name as city_name'
            );
        if ($user->role !== 'Admin') {
            $query->where('hospitals.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $query = $query->orderBy('invoices.date', 'desc')->get();

        $totalAmount = $query->sum('amount');

        $setting = Setting::first();
        $image = public_path($setting->logo);

        $pdf = Pdf::loadView('admin.reports.invoice_pdf', [
            'invoices' => $query,
            'totalAmount' => $totalAmount,
            'setting' => $setting,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'invoice-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function generalReport(){
        $user = auth()->user();
        $allowedCities = [];

        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }
        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();

        $expenses = ExpenseType::where('trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user) {

                if ($user->role === 'Manager') {
                    $q->where('owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('owner_id', $user->parent_id);
                }
            })
            ->get();

        return view('admin.reports.general', compact('cities', 'expenses'));
    }

    public function generalReportAjax(Request $request){
        $user = auth()->user();
        $search = $request->input('search.value');
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $city      = $request->city;
        $payment_type = $request->payment_type;

        $expenses = Expense::join('expense_types', 'expense_types.id', '=', 'expenses.expense_type_id')
            ->leftJoin('cities', 'expense_types.city_id', '=', 'cities.id')
            ->select(
                'expenses.created_at as date',
                DB::raw("CONCAT('Expense (', expense_types.name, ' (', COALESCE(cities.short_name, 'PK'), '))') as payment_type"),
                DB::raw('CAST(0 AS DECIMAL(12,2)) as credit'),
                DB::raw('CAST(expenses.amount AS DECIMAL(12,2)) as debit'),
                DB::raw('COALESCE(cities.id, 0) as city_id'),
                'expense_types.id as expense_type_id' 
            );
        if ($user->role !== 'Admin') {
            $expenses->where('expense_types.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $salaries = Salary::join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->select(
                'salaries.created_at as date',
                DB::raw("CONCAT('Salary (', employees.name, ' (', cities.short_name, '))') as payment_type"),
                DB::raw('CAST(0 AS DECIMAL(12,2)) as credit'),
                DB::raw('CAST(( COALESCE(salaries.amount,0) + COALESCE(salaries.allowance,0) + COALESCE(salaries.bonus,0)) AS DECIMAL(12,2)) as debit'),
                DB::raw('COALESCE(cities.id, 0) as city_id'),
                DB::raw('NULL as expense_type_id')
            );

        if ($user->role !== 'Admin') {
            $salaries->where('employees.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $invoices = Invoice::join('hospitals', 'hospitals.id', '=', 'invoices.hospital_id')->leftJoin('cities', 'hospitals.city_id', '=', 'cities.id')->select(
            'invoices.date as date',
            DB::raw("CONCAT('Invoice (', hospitals.company_name, ' (', cities.short_name, '))') as payment_type"),
            DB::raw('CAST(amount AS DECIMAL(12,2)) as credit'),
            DB::raw('CAST(0 AS DECIMAL(12,2)) as debit'),
            DB::raw('COALESCE(cities.id, 0) as city_id'),
            DB::raw('NULL as expense_type_id') 
        );

        if ($user->role !== 'Admin') {
            $invoices->where('hospitals.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $data = $expenses->unionAll($salaries)->unionAll($invoices);

        $query = DB::query()->fromSub($data, 't');

        if(!empty($search)){
            $query->where(function($q) use ($search){
                $q->where('payment_type', 'like', "%{$search}%")
                ->orWhere('credit', 'like', "%{$search}%")
                ->orWhere('debit', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('date', [$from, $to]);
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('city_id', $city);
        }

        if (!empty($payment_type) && is_array($payment_type)) {
            $query->where(function($q) use ($payment_type) {
                $expIds = array_filter($payment_type, fn($p) => is_numeric($p));
                if (!empty($expIds)) {
                    $q->orWhereIn('expense_type_id', $expIds);
                }

                $otherTypes = array_filter($payment_type, fn($p) => !is_numeric($p));
                foreach ($otherTypes as $type) {
                    $q->orWhere('payment_type', 'like', "%{$type}%");
                }
            });
        }

        $recordsTotal = DB::query()->fromSub($data, 't')->count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('date', 'asc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        $sumCredit = $results->sum('credit');
        $sumDebit  = $results->sum('debit');

        $runningBalance = 0;
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'sumCredit' => number_format($sumCredit, 2),
            'sumDebit'  => number_format($sumDebit, 2),
            'data' => $results->map(function($row) use (&$runningBalance){
                $runningBalance = $runningBalance + $row->credit - $row->debit;
                return [
                    'date' => Carbon::parse($row->date)->format('d-m-Y'),
                    'payment_type' => $row->payment_type,
                    'credit' => number_format(abs($row->credit)),
                    'debit' => number_format(abs($row->debit)),
                    'amount' => number_format(abs($runningBalance)),
                    'amount_sign' => $runningBalance >= 0 ? 'positive' : 'negative'
                ];

            })
        ]);
    }

    public function generalReportPdf(){
        $user = auth()->user();
        $expenses = Expense::join('expense_types', 'expense_types.id', '=', 'expenses.expense_type_id')
            ->leftJoin('cities', 'expense_types.city_id', '=', 'cities.id')
            ->select(
                'expenses.created_at as date',
                DB::raw("CONCAT('Expense (', expense_types.name, ' (', COALESCE(cities.short_name, 'PK'), '))') as payment_type"),
                DB::raw('CAST(0 AS DECIMAL(12,2)) as credit'),
                DB::raw('CAST(expenses.amount AS DECIMAL(12,2)) as debit')
            );

        if (auth()->user()->role !== 'Admin') {
            $expenses->where('expense_types.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $salaries = Salary::join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->select(
                'salaries.created_at as date',
                DB::raw("CONCAT('Salary (', employees.name, ' (', cities.short_name, '))') as payment_type"),
                DB::raw('CAST(0 AS DECIMAL(12,2)) as credit'),
                DB::raw('CAST(( COALESCE(salaries.amount,0) + COALESCE(salaries.allowance,0) + COALESCE(salaries.bonus,0)) AS DECIMAL(12,2)) as debit'),
            );

        if (auth()->user()->role !== 'Admin') {
            $salaries->where('employees.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $invoices = Invoice::join('hospitals', 'hospitals.id', '=', 'invoices.hospital_id')
            ->leftJoin('cities', 'hospitals.city_id', '=', 'cities.id')
            ->select(
                'invoices.date as date',
                DB::raw("CONCAT('Invoice (', hospitals.company_name, ' (', cities.short_name, '))') as payment_type"),
                DB::raw('CAST(amount AS DECIMAL(12,2)) as credit'),
                DB::raw('CAST(0 AS DECIMAL(12,2)) as debit')
            );

        if (auth()->user()->role !== 'Admin') {
            $invoices->where('hospitals.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $data = $expenses->unionAll($salaries)->unionAll($invoices);

        $results = DB::query()
            ->fromSub($data, 't')
            ->orderBy('date', 'asc')
            ->get();

        $runningBalance = 0;
        foreach($results as $row){
            $runningBalance += $row->credit - $row->debit;
            $row->running_balance = $runningBalance;
        }

        $settings = Setting::first();
        $image = public_path($settings->logo);
        $pdf = Pdf::loadView('admin.reports.general_pdf', [
            'records' => $results,
            'setting' => $settings,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'landscape');

        $filename = 'general-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function wasteReport(){
        $user = auth()->user();
        $allowedCities = [];

        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();

        $companies = Hospital::when($user->role !== 'Admin', function ($q) use ($user) {

            if ($user->role === 'Manager') {
                $q->where('owner_id', $user->id);
            } elseif ($user->role === 'Employee') {
                $q->where('owner_id', $user->parent_id);
            }
        })->get();

        return view('admin.reports.waste', compact('cities', 'companies'));
    }

    public function wasteReportAjax(Request $request){
        $user = auth()->user();
        $search = $request->input('search.value');
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $city      = $request->city;
        $hospital   = $request->hospital;

        $query = Waste::join('hospitals', 'hospitals.id', '=', 'wastes.hospital_id')
        ->leftjoin('cities', 'hospitals.city_id', '=', 'cities.id')
        ->select(
            'wastes.date',
            'wastes.infections',
            'wastes.infections_bags',
            'wastes.sharp',
            'wastes.sharp_bags',
            'wastes.pharmaceutical',
            'wastes.pharmaceutical_bags',
            'wastes.chemical',
            'wastes.chemical_bags',
            'wastes.pathological',
            'wastes.pathological_bags',
            'wastes.total_weight',
            'wastes.total_bags',
            'hospitals.company_name',
            'cities.short_name',
        );

        if ($user->role !== 'Admin') {
            $query->where('hospitals.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('wastes.date', 'like', "%{$search}%")
                ->orWhere('wastes.infections', 'like', "%{$search}%")
                ->orWhere('wastes.infections_bags', 'like', "%{$search}%")
                ->orWhere('wastes.pharmaceutical', 'like', "%{$search}%")
                ->orWhere('wastes.pharmaceutical_bags', 'like', "%{$search}%")
                ->orWhere('wastes.chemical', 'like', "%{$search}%")
                ->orWhere('wastes.chemical_bags', 'like', "%{$search}%")
                ->orWhere('wastes.pathological', 'like', "%{$search}%")
                ->orWhere('wastes.pathological_bags', 'like', "%{$search}%")
                ->orWhere('wastes.sharp', 'like', "%{$search}%")
                ->orWhere('wastes.sharp_bags', 'like', "%{$search}%")
                ->orWhere('wastes.total_weight', 'like', "%{$search}%")
                ->orWhere('cities.short_name', 'like', "%{$search}%")
                ->orWhere('hospitals.company_name', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('wastes.date', [$from, $to]);
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('hospitals.city_id', $city);
        }

        if (!empty($hospital) && is_array($hospital)) {
            $query->whereIn('hospitals.id', $hospital);
        }

        $recordsTotal = Waste::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('wastes.date', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();
    
            $sumInfections      = $results->sum('infections');
            $sumInfectionsBags  = $results->sum('infections_bags');

            $sumSharp           = $results->sum('sharp');
            $sumSharpBags       = $results->sum('sharp_bags');

            $sumChemical        = $results->sum('chemical');
            $sumChemicalBags    = $results->sum('chemical_bags');

            $sumPharma          = $results->sum('pharmaceutical');
            $sumPharmaBags      = $results->sum('pharmaceutical_bags');

            $sumPatho           = $results->sum('pathological');
            $sumPathoBags       = $results->sum('pathological_bags');

            $sumWeight          = $results->sum('total_weight');
            $sumWeightBags      = $results->sum('total_bags');

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'sumInfections' => $sumInfections . ' KG (' . $sumInfectionsBags . ')',
            'sumSharp'      => $sumSharp . ' KG (' . $sumSharpBags . ')',
            'sumChemical'   => $sumChemical . ' KG (' . $sumChemicalBags . ')',
            'sumPharmaceutical' => $sumPharma . ' KG (' . $sumPharmaBags . ')',
            'sumPathological'   => $sumPatho . ' KG (' . $sumPathoBags . ')',
            'sumWeight'         => $sumWeight . ' KG (' . $sumWeightBags . ')',

            'data' => $results->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->date)->format('d-m-Y'),
                    'company_name' => $row->company_name.' ('.$row->short_name.')',
                    'infections' => ($row->infections ?? 0) .' KG'. ' ('.($row->infections_bags ?? 0) .')',
                    'sharp' => ($row->sharp ?? 0) .' KG'. ' ('.($row->sharp_bags ?? 0) .')',
                    'chemical' => ($row->chemical ?? 0) .' KG'. ' ('.($row->chemical_bags ?? 0) .')',
                    'pharmaceutical' => ($row->pharmaceutical ?? 0) .' KG'. ' ('.($row->pharmaceutical_bags ?? 0) .')',
                    'pathological' => ($row->pathological ?? 0) .' KG'. ' ('.($row->pathological_bags ?? 0) .')',
                    'total_weight' => $row->total_weight . ' KG'. ' ('.($row->total_bags ?? 0) .')',
                ];
            }),
        ]);
    }

    public function wasteReportPdf(){
        $user = auth()->user();
        $query = Waste::join('hospitals', 'hospitals.id', '=', 'wastes.hospital_id')
            ->leftJoin('cities', 'hospitals.city_id', '=', 'cities.id')
            ->select(
                'wastes.date',
                'wastes.infections',
                'wastes.infections_bags',
                'wastes.chemical',
                'wastes.chemical_bags',
                'wastes.pharmaceutical',
                'wastes.pharmaceutical_bags',
                'wastes.pathological',
                'wastes.pathological_bags',
                'wastes.sharp',
                'wastes.sharp_bags',
                'wastes.total_weight',
                'hospitals.company_name',
                'cities.short_name'
            );

        if ($user->role !== 'Admin') {
            $query->where('hospitals.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $results = $query->orderBy('wastes.date', 'desc')->get();

        $setting = Setting::first();
        $image = public_path($setting->logo);
        $pdf = Pdf::loadView('admin.reports.waste_pdf', [
            'wastes' => $results,
            'setting' => $setting,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'landscape'); 

        $filename = 'waste-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function salaryReport(){
        $user = auth()->user();

        $allowedCities = [];

        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();

        return view('admin.reports.salary', compact('cities'));
    }

    public function salaryReportajax(Request $request){
        $user = auth()->user();
        $search = $request->input('search.value');
        $city   = $request->city;
        $includeTax = $request->includeTax;

        $setting = Setting::first();
        $limit = $setting->salary_limit ?? 40000;
        if($includeTax){
            $salarySelect = DB::raw("
                CASE 
                    WHEN (
                        COALESCE(salaries.amount,0) + 
                        COALESCE(salaries.allowance,0) + 
                        COALESCE(salaries.bonus,0)
                    ) < {$limit}
                    THEN {$limit} 
                    ELSE (
                        COALESCE(salaries.amount,0) + 
                        COALESCE(salaries.allowance,0) + 
                        COALESCE(salaries.bonus,0)
                    )
                END as total_salary
            ");
        } else {
            $salarySelect = DB::raw("
                (
                    COALESCE(salaries.amount,0) + 
                    COALESCE(salaries.allowance,0) + 
                    COALESCE(salaries.bonus,0)
                ) as total_salary
            ");
        }

        $query = Salary::join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->select(
                'employees.name',
                'cities.name as city_name',
                $salarySelect
            );

        if($user->role !== 'Admin') {
            $query->where('employees.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('employees.name', 'like', "%{$search}%")
                ->orWhere('cities.name', 'like', "%{$search}%")
                ->orWhere(DB::raw("(salaries.amount + salaries.allowance + salaries.bonus)"), 'like', "%{$search}%");
            });
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('employees.city_id', $city);
        }

        $recordsTotal = Salary::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('employees.name', 'asc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();
        
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $results->map(function ($row) {
                return [
                    'name' => $row->name,
                    'city_name' => $row->city_name,
                    'salary' => number_format($row->total_salary),
                ];
            }),
        ]);
    }

    public function salaryReportPdf(Request $request){
        $user = auth()->user();
        $city = $request->city;
        $includeTax = $request->includeTax;

        $setting = Setting::first();
        $limit = $setting->salary_limit ?? 40000;

        if($includeTax){
            $salarySelect = DB::raw("
                CASE 
                    WHEN (
                        COALESCE(salaries.amount,0) + 
                        COALESCE(salaries.allowance,0) + 
                        COALESCE(salaries.bonus,0)
                    ) < {$limit}
                    THEN {$limit} 
                    ELSE (
                        COALESCE(salaries.amount,0) + 
                        COALESCE(salaries.allowance,0) + 
                        COALESCE(salaries.bonus,0)
                    )
                END as total_salary
            ");
        } else {
            $salarySelect = DB::raw("
                (
                    COALESCE(salaries.amount,0) + 
                    COALESCE(salaries.allowance,0) + 
                    COALESCE(salaries.bonus,0)
                ) as total_salary
            ");
        }

        $query = Salary::join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->select(
                'employees.name',
                'cities.name as city_name',
                'salaries.date as salary_date',
                $salarySelect
            );

        if ($user->role !== 'Admin') {
            $query->where('employees.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('employees.city_id', $city);
        }

        $results = $query->orderBy('salaries.date', 'desc')->get();

        $image = public_path($setting->logo);
        $pdf = Pdf::loadView('admin.reports.salary_pdf', [
            'results' => $results,
            'image' => $image,
            'setting' => $setting
        ]);

        $pdf->setPaper('A4', 'landscape'); 

        $filename = 'salary-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function saleReport(){
        $user = auth()->user();
        $hospitalsQuery = Hospital::where('trash', 0);
        if ($user->role !== 'Admin') {
            $hospitalsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $hospitals = $hospitalsQuery->get();
        return view('admin.reports.sale', compact('hospitals'));
    }

    public function saleReportajax(Request $request){
        $search = $request->input('search.value');
        $user   = auth()->user();
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $hospitals      = $request->hospitals;

        $query = TotalSale::join('hospitals', 'hospitals.id', '=', 'total_sales.hospital_id')
        ->select(
            'total_sales.sale_date',
            'total_sales.total',
            'hospitals.company_name',
        );

        if ($user->role !== 'Admin') {
            $query->where('total_sales.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('total_sales.sale_date', 'like', "%{$search}%")
                ->orWhere('total_sales.total', 'like', "%{$search}%")
                ->orWhere('hospitals.company_name', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('total_sales.sale_date', [$from, $to]);
        }

        if (!empty($hospitals) && is_array($hospitals)) {
            $query->whereIn('hospitals.id', $hospitals);
        }

        $recordsTotal = TotalSale::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('total_sales.sale_date', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        
        $totalAmount = $results->sum('total');

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'totalAmount' => number_format($totalAmount),
            'data' => $results->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->date)->format('d-m-Y'),
                    'company_name' => $row->company_name,
                    'amount' => number_format($row->total),
                ];
            }),
        ]);
    }

    public function saleReportPdf(Request $request){
        $user = auth()->user();
        $query = TotalSale::join('hospitals', 'hospitals.id', '=', 'total_sales.hospital_id')
            ->select(
                'total_sales.sale_date',
                'total_sales.total',
                'hospitals.company_name',
            );

        if ($user->role !== 'Admin') {
            $query->where('total_sales.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $query = $query->orderBy('total_sales.sale_date', 'desc')->get();

        $totalAmount = $query->sum('total');

        $setting = Setting::first();
        $image = public_path($setting->logo);

        $pdf = Pdf::loadView('admin.reports.sale_pdf', [
            'sales' => $query,
            'totalAmount' => $totalAmount,
            'setting' => $setting,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'sale-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function purchaseReport(){
        return view('admin.reports.purchase');
    }

    public function purchaseReportajax(Request $request){
        $search = $request->input('search.value');
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $user      = auth()->user();

        $query = TotalPurchase::query();
        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_date', 'like', "%{$search}%")
                ->orWhere('total', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('purchase_date', [$from, $to]);
        }

        $recordsTotal = TotalPurchase::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('purchase_date', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        $totalAmount = $results->sum('total');

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'totalAmount' => number_format($totalAmount, 2),
            'data' => $results->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->purchase_date)->format('d-m-Y'),
                    'amount' => number_format($row->total, 2),
                ];
            }),
        ]);
    }

    public function purchaseReportPdf(Request $request){
        $user = auth()->user();
        $query = TotalPurchase::query();
        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $query = $query->orderBy('purchase_date', 'desc')->get();
        $totalAmount = $query->sum('total');

        $setting = Setting::first();
        $image = public_path($setting->logo);

        $pdf = Pdf::loadView('admin.reports.purchase_pdf', [
            'purchases' => $query,
            'totalAmount' => $totalAmount,
            'setting' => $setting,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'purchase-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function rentReport(){
        $user = auth()->user();
        $hospitalsQuery = Hospital::where('trash', 0);
        if ($user->role !== 'Admin') {
            $hospitalsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $hospitals = $hospitalsQuery->get();

        return view('admin.reports.rent', compact('hospitals'));
    }

    public function rentReportajax(Request $request){
        $search = $request->input('search.value');
        $user   = auth()->user();
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $hospitals      = $request->hospitals;

        $query = TotalRent::join('hospitals', 'hospitals.id', '=', 'total_rents.hospital_id')
        ->select(
            'total_rents.start_date',
            'total_rents.end_date',
            'total_rents.total',
            'hospitals.company_name',
        );

        if ($user->role !== 'Admin') {
            $query->where('total_rents.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('total_rents.start_date', 'like', "%{$search}%")
                ->orWhere('total_rents.end_date', 'like', "%{$search}%")
                ->orWhere('total_rents.total', 'like', "%{$search}%")
                ->orWhere('hospitals.company_name', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->where(function($q) use ($from, $to){
                $q->whereBetween('total_rents.start_date', [$from, $to])
                ->orWhereBetween('total_rents.end_date', [$from, $to])
                ->orWhere(function($qq) use ($from, $to){
                    $qq->where('total_rents.start_date', '<=', $from)
                        ->where('total_rents.end_date', '>=', $to);
                });
            });
        }

        if (!empty($hospitals) && is_array($hospitals)) {
            $query->whereIn('hospitals.id', $hospitals);
        }

        $recordsTotal = TotalRent::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('total_rents.id', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        
        $totalAmount = $results->sum('total');

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'totalAmount' => number_format($totalAmount),
            'data' => $results->map(function ($row) {
                return [
                    'company_name' => $row->company_name,
                    'start_date' => Carbon::parse($row->start_date)->format('d-m-Y'),
                    'end_date' => Carbon::parse($row->end_date)->format('d-m-Y'),
                    'amount' => number_format($row->total),
                ];
            }),
        ]);
    }

    public function rentReportPdf(Request $request){
        $query = TotalRent::join('hospitals', 'hospitals.id', '=', 'total_rents.hospital_id')
            ->select(
                'total_rents.start_date',
                'total_rents.end_date',
                'total_rents.total',
                'hospitals.company_name',
            );

        if ($user->role !== 'Admin') {
            $query->where('total_rents.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $query = $query->orderBy('total_rents.id', 'desc')->get();

        $totalAmount = $query->sum('total');

        $setting = Setting::first();
        $image = public_path($setting->logo);

        $pdf = Pdf::loadView('admin.reports.rent_pdf', [
            'rents' => $query,
            'totalAmount' => $totalAmount,
            'setting' => $setting,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'rent-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function disposalWasteReport(){
        $user = auth()->user();
        $allowedCities = [];

        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();
        return view('admin.reports.waste_disposal', compact('cities'));
    }

    public function disposalWasteReportajax(Request $request){
        $user = auth()->user();
        $search = $request->input('search.value');
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $city      = $request->city;

        $allowedCities = [];
        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $query = WasteDisposal::leftjoin('cities', 'waste_disposals.city_id', '=', 'cities.id')
        ->select(
            'waste_disposals.date',
            'waste_disposals.infections',
            'waste_disposals.infections_bags',
            'waste_disposals.sharp',
            'waste_disposals.sharp_bags',
            'waste_disposals.pharmaceutical',
            'waste_disposals.pharmaceutical_bags',
            'waste_disposals.chemical',
            'waste_disposals.chemical_bags',
            'waste_disposals.pathological',
            'waste_disposals.pathological_bags',
            'waste_disposals.total_weight',
            'waste_disposals.total_bags',
            'cities.short_name',
             'cities.name as city_name'
        )
        ->when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('waste_disposals.city_id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        });

        if ($user->role !== 'Admin') {
            if (!empty($user->city_ids)) {
                $assignedCityIds = array_filter(explode(',', $user->city_ids));
                if (count($assignedCityIds)) {
                    $query->whereIn('waste_disposals.city_id', $assignedCityIds);
                } else {
                    $query->whereRaw('0 = 1');
                }
            } else {
                $query->whereRaw('0 = 1');
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('waste_disposals.date', 'like', "%{$search}%")
                ->orWhere('waste_disposals.infections', 'like', "%{$search}%")
                ->orWhere('waste_disposals.infections_bags', 'like', "%{$search}%")
                ->orWhere('waste_disposals.pharmaceutical', 'like', "%{$search}%")
                ->orWhere('waste_disposals.pharmaceutical_bags', 'like', "%{$search}%")
                ->orWhere('waste_disposals.chemical', 'like', "%{$search}%")
                ->orWhere('waste_disposals.chemical_bags', 'like', "%{$search}%")
                ->orWhere('waste_disposals.pathological', 'like', "%{$search}%")
                ->orWhere('waste_disposals.pathological_bags', 'like', "%{$search}%")
                ->orWhere('waste_disposals.sharp', 'like', "%{$search}%")
                ->orWhere('waste_disposals.sharp_bags', 'like', "%{$search}%")
                ->orWhere('waste_disposals.total_weight', 'like', "%{$search}%")
                ->orWhere('cities.short_name', 'like', "%{$search}%")
                ->orWhere('cities.name', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('waste_disposals.date', [$from, $to]);
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('waste_disposals.city_id', $city);
        }

        $recordsTotal = WasteDisposal::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('waste_disposals.date', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();
    
            $sumInfections      = $results->sum('infections');
            $sumInfectionsBags  = $results->sum('infections_bags');

            $sumSharp           = $results->sum('sharp');
            $sumSharpBags       = $results->sum('sharp_bags');

            $sumChemical        = $results->sum('chemical');
            $sumChemicalBags    = $results->sum('chemical_bags');

            $sumPharma          = $results->sum('pharmaceutical');
            $sumPharmaBags      = $results->sum('pharmaceutical_bags');

            $sumPatho           = $results->sum('pathological');
            $sumPathoBags       = $results->sum('pathological_bags');

            $sumWeight          = $results->sum('total_weight');
            $sumWeightBags      = $results->sum('total_bags');

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'sumInfections' => $sumInfections . ' KG (' . $sumInfectionsBags . ')',
            'sumSharp'      => $sumSharp . ' KG (' . $sumSharpBags . ')',
            'sumChemical'   => $sumChemical . ' KG (' . $sumChemicalBags . ')',
            'sumPharmaceutical' => $sumPharma . ' KG (' . $sumPharmaBags . ')',
            'sumPathological'   => $sumPatho . ' KG (' . $sumPathoBags . ')',
            'sumWeight'         => $sumWeight . ' KG (' . $sumWeightBags . ')',

            'data' => $results->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->date)->format('d-m-Y'),
                    'city_name' => $row->city_name,
                    'infections' => ($row->infections ?? 0) .' KG'. ' ('.($row->infections_bags ?? 0) .')',
                    'sharp' => ($row->sharp ?? 0) .' KG'. ' ('.($row->sharp_bags ?? 0) .')',
                    'chemical' => ($row->chemical ?? 0) .' KG'. ' ('.($row->chemical_bags ?? 0) .')',
                    'pharmaceutical' => ($row->pharmaceutical ?? 0) .' KG'. ' ('.($row->pharmaceutical_bags ?? 0) .')',
                    'pathological' => ($row->pathological ?? 0) .' KG'. ' ('.($row->pathological_bags ?? 0) .')',
                    'total_weight' => $row->total_weight . ' KG'. ' ('.($row->total_bags ?? 0) .')',
                ];
            }),
        ]);
    }

    public function disposalWasteReportPdf(){
        $user = auth()->user();
        $allowedCities = [];
        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $query = WasteDisposal::leftJoin('cities', 'waste_disposals.city_id', '=', 'cities.id')
            ->select(
                'waste_disposals.date',
                'waste_disposals.infections',
                'waste_disposals.infections_bags',
                'waste_disposals.chemical',
                'waste_disposals.chemical_bags',
                'waste_disposals.pharmaceutical',
                'waste_disposals.pharmaceutical_bags',
                'waste_disposals.pathological',
                'waste_disposals.pathological_bags',
                'waste_disposals.sharp',
                'waste_disposals.sharp_bags',
                'waste_disposals.total_weight',
                'cities.short_name',
                'cities.name as city_name'
            )
            ->when($user->role !== 'Admin', function ($q) use ($allowedCities) {
                if (!empty($allowedCities)) {
                    $q->whereIn('waste_disposals.city_id', $allowedCities);
                } else {
                    $q->whereRaw('0 = 1');
                }
            });

        $results = $query->orderBy('waste_disposals.date', 'desc')->get();

        $setting = Setting::first();
        $image = public_path($setting->logo);
        $pdf = Pdf::loadView('admin.reports.waste_disposal_pdf', [
            'wastes' => $results,
            'setting' => $setting,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'landscape'); 

        $filename = 'waste-disposal-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

    public function burnWasteReport(){
        $user = auth()->user();
        $allowedCities = [];

        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();

        return view('admin.reports.waste_burn', compact('cities'));
    }

    public function burnWasteReportajax(Request $request){
        $user = auth()->user();
        $search = $request->input('search.value');
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $city      = $request->city;

        $allowedCities = [];
        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $query = WasteBurn::leftjoin('cities', 'waste_burn.city_id', '=', 'cities.id')
        ->select(
            'waste_burn.date',
            'waste_burn.infections',
            'waste_burn.infections_bags',
            'waste_burn.sharp',
            'waste_burn.sharp_bags',
            'waste_burn.pharmaceutical',
            'waste_burn.pharmaceutical_bags',
            'waste_burn.chemical',
            'waste_burn.chemical_bags',
            'waste_burn.pathological',
            'waste_burn.pathological_bags',
            'waste_burn.total_weight',
            'waste_burn.total_bags',
            'cities.short_name',
            'cities.name as city_name'
        )
        ->when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('waste_burn.city_id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('waste_burn.date', 'like', "%{$search}%")
                ->orWhere('waste_burn.infections', 'like', "%{$search}%")
                ->orWhere('waste_burn.infections_bags', 'like', "%{$search}%")
                ->orWhere('waste_burn.pharmaceutical', 'like', "%{$search}%")
                ->orWhere('waste_burn.pharmaceutical_bags', 'like', "%{$search}%")
                ->orWhere('waste_burn.chemical', 'like', "%{$search}%")
                ->orWhere('waste_burn.chemical_bags', 'like', "%{$search}%")
                ->orWhere('waste_burn.pathological', 'like', "%{$search}%")
                ->orWhere('waste_burn.pathological_bags', 'like', "%{$search}%")
                ->orWhere('waste_burn.sharp', 'like', "%{$search}%")
                ->orWhere('waste_burn.sharp_bags', 'like', "%{$search}%")
                ->orWhere('waste_burn.total_weight', 'like', "%{$search}%")
                ->orWhere('cities.short_name', 'like', "%{$search}%")
                ->orWhere('cities.name', 'like', "%{$search}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay();

            $query->whereBetween('waste_burn.date', [$from, $to]);
        }

        if (!empty($city) && is_array($city)) {
            $query->whereIn('waste_burn.city_id', $city);
        }

        $recordsTotal = WasteBurn::count();
        $recordsFiltered = $query->count();

        $results = $query
            ->orderBy('waste_burn.date', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();
    
            $sumInfections      = $results->sum('infections');
            $sumInfectionsBags  = $results->sum('infections_bags');

            $sumSharp           = $results->sum('sharp');
            $sumSharpBags       = $results->sum('sharp_bags');

            $sumChemical        = $results->sum('chemical');
            $sumChemicalBags    = $results->sum('chemical_bags');

            $sumPharma          = $results->sum('pharmaceutical');
            $sumPharmaBags      = $results->sum('pharmaceutical_bags');

            $sumPatho           = $results->sum('pathological');
            $sumPathoBags       = $results->sum('pathological_bags');

            $sumWeight          = $results->sum('total_weight');
            $sumWeightBags      = $results->sum('total_bags');

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'sumInfections' => $sumInfections . ' KG (' . $sumInfectionsBags . ')',
            'sumSharp'      => $sumSharp . ' KG (' . $sumSharpBags . ')',
            'sumChemical'   => $sumChemical . ' KG (' . $sumChemicalBags . ')',
            'sumPharmaceutical' => $sumPharma . ' KG (' . $sumPharmaBags . ')',
            'sumPathological'   => $sumPatho . ' KG (' . $sumPathoBags . ')',
            'sumWeight'         => $sumWeight . ' KG (' . $sumWeightBags . ')',

            'data' => $results->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->date)->format('d-m-Y'),
                    'city_name' => $row->city_name,
                    'infections' => ($row->infections ?? 0) .' KG'. ' ('.($row->infections_bags ?? 0) .')',
                    'sharp' => ($row->sharp ?? 0) .' KG'. ' ('.($row->sharp_bags ?? 0) .')',
                    'chemical' => ($row->chemical ?? 0) .' KG'. ' ('.($row->chemical_bags ?? 0) .')',
                    'pharmaceutical' => ($row->pharmaceutical ?? 0) .' KG'. ' ('.($row->pharmaceutical_bags ?? 0) .')',
                    'pathological' => ($row->pathological ?? 0) .' KG'. ' ('.($row->pathological_bags ?? 0) .')',
                    'total_weight' => $row->total_weight . ' KG'. ' ('.($row->total_bags ?? 0) .')',
                ];
            }),
        ]);
    }

    public function burnWasteReportPdf(){
        $user = auth()->user();

        $allowedCities = [];
        if ($user->role === 'Manager' && $user->city_ids) {
            $allowedCities = array_filter(explode(',', $user->city_ids));
        } elseif ($user->role === 'Employee' && $user->parent_id) {
            $parent = User::find($user->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }
        $query = WasteBurn::leftJoin('cities', 'waste_burn.city_id', '=', 'cities.id')
            ->select(
                'waste_burn.date',
                'waste_burn.infections',
                'waste_burn.infections_bags',
                'waste_burn.chemical',
                'waste_burn.chemical_bags',
                'waste_burn.pharmaceutical',
                'waste_burn.pharmaceutical_bags',
                'waste_burn.pathological',
                'waste_burn.pathological_bags',
                'waste_burn.sharp',
                'waste_burn.sharp_bags',
                'waste_burn.total_weight',
                'cities.short_name',
                'cities.name as city_name'
            )
            ->when($user->role !== 'Admin', function ($q) use ($allowedCities) {
                if (!empty($allowedCities)) {
                    $q->whereIn('waste_burn.city_id', $allowedCities);
                } else {
                    $q->whereRaw('0 = 1');
                }
            });

        $results = $query->orderBy('waste_burn.date', 'desc')->get();

        $setting = Setting::first();
        $image = public_path($setting->logo);
        $pdf = Pdf::loadView('admin.reports.waste_burn_pdf', [
            'wastes' => $results,
            'setting' => $setting,
            'image' => $image
        ]);

        $pdf->setPaper('A4', 'landscape'); 

        $filename = 'waste-burn-report-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }
}
