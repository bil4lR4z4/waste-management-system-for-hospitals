<?php

namespace App\Http\Controllers;
use App\Models\ExpenseType;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(){
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

        $expense_types = ExpenseType::leftJoin('cities', 'expense_types.city_id', '=', 'cities.id')
            ->where('expense_types.trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user, $allowedCities) {
                if ($user->role === 'Manager') {
                    $q->where('expense_types.owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('expense_types.owner_id', $user->parent_id);
                }

                if (!empty($allowedCities)) {
                    $q->whereIn('expense_types.city_id', $allowedCities);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->select('expense_types.*', 'cities.short_name')
            ->get();

        return view('admin.expense.index', compact('expense_types'));
    }

    public function store(Request $request){
        $request->validate([
            'expense_type_id' => 'required',
            'amount' => 'required',
        ]);
        
        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $expense = new Expense();
        $expense->expense_type_id = $request->expense_type_id;
        $expense->amount = $request->amount;
        $expense->owner_id = $ownerId;
        $expense->save();

        return redirect()->route('expense.index')->with('success', 'Expense Created Successfully!');
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';
        $user = auth()->user();

        $query = Expense::Join('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')->leftJoin('cities', 'expense_types.city_id', '=', 'cities.id')
        ->where('expense_types.trash', 0)
                    ->select(
                        'expenses.*',
                        'expense_types.name as expense_name',
                        'cities.short_name',
                        'cities.name as city_name'
                    );

        if (auth()->user()->role !== 'Admin') {
            $query->where('expenses.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('expense_types.name', 'like', "%$search%")
                ->orWhere('cities.short_name', 'like', "%$search%")
                ->orWhere('cities.name', 'like', "%$search%")
                ->orWhere('expenses.amount', 'like', "%$search%");
            });
        }

        $total = $query->count();

        $expenses = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('expenses.id','desc')
                    ->get();

        $authUser = auth()->user(); 
        $data = [];
        foreach ($expenses as $expense) {
            $action = '';
            if ($authUser->hasPermission('expense', 'edit')) {
                $action .= '
                    <a class="btn btn-primary btn-sm" href="'.route('expense.edit', $expense->id).'"><i class="fa-solid fa-pen-to-square"></i></a>
                ';
            }

            if ($authUser->hasPermission('expense', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete" data-action="'.route('expense.destroy', $expense->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }
            $data[] = [
                'expense_name' => $expense->expense_name . ' (' . ($expense->short_name ?? 'Pk') . ')',
                'amount' => $expense->amount ?? '-',
                'action'      => $action
            ];
        }

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }

    public function edit($id){
        $user = auth()->user();

        $query = Expense::where('id', $id);
        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $edit = $query->first();

        if (!$edit) {
            return redirect()->route('expense.index')
                ->with('error', 'Record not found or access denied!');
        }

        $allowedCities = [];
        if ($user->role !== 'Admin' && $user->city_ids) {
            $allowedCities = explode(',', $user->city_ids);
        }

        $expense_types = ExpenseType::leftJoin('cities', 'expense_types.city_id', '=', 'cities.id')
            ->where('expense_types.trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user, $allowedCities) {
                if ($user->role === 'Manager') {
                    $q->where('expense_types.owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('expense_types.owner_id', $user->parent_id);
                }

                if (!empty($allowedCities)) {
                    $q->whereIn('expense_types.city_id', $allowedCities);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->select('expense_types.*', 'cities.short_name')
            ->get();

        return view('admin.expense.index', compact('expense_types', 'edit'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'expense_type_id' => 'required',
            'amount' => 'required',
        ]);

        $expense = Expense::find($id);
        $expense->expense_type_id = $request->expense_type_id;
        $expense->amount = $request->amount;
        $expense->save();

        return redirect()->route('expense.index')->with('success', 'Expense Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();

        $query = Expense::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $expense = $query->first();

        if (!$expense) {
            return redirect()->route('expense.index')
                ->with('error', 'Record not found!');
        }

        $expense->update(['trash' => 1]);

        return redirect()->route('expense.index')
            ->with('success', 'Expense Deleted Successfully!');
    }

}
