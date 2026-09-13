<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller{
    public function index(){
        $authUser = auth()->user();

        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null; 
        }

        if ($authUser->role === 'Admin') {
            $totalEmployees = Employee::where('trash', 0)->count();
        } else {
            $totalEmployees = Employee::where('trash', 0)
                                    ->where('owner_id', $ownerId)
                                    ->count();
        }

        $query = Expense::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);

        if ($authUser->role !== 'Admin') {
            $query->where('owner_id', $ownerId);
        }

        $totalExpenses = $query->sum('amount');

        $invoiceQuery = Invoice::query();
        if ($authUser->role !== 'Admin') {
            $invoiceQuery->where('owner_id', $ownerId);
        }

        $totalIncomes = $invoiceQuery->sum('amount');
        return view('admin.index', compact('totalEmployees', 'totalExpenses', 'totalIncomes'));
    }
}
