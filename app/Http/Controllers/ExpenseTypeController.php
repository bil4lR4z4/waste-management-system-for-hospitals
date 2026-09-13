<?php

namespace App\Http\Controllers;
use App\Models\ExpenseType;
use App\Models\Expense;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
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
            ->select('expense_types.*', 'cities.name as city_name')
            ->latest()
            ->get();

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('1=0'); 
            }
        })->get();

        return view('admin.expense_type.index', compact('expense_types', 'cities'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|max:255',
            'city_id' => 'nullable|integer',
        ]);

        
        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $expense_type = new ExpenseType();
        $expense_type->name = $request->name;
        $expense_type->city_id = $request->city_id;
        $expense_type->owner_id = $ownerId;
        $expense_type->save();

        return redirect()->route('expense_type.index')->with('success', 'Expense Type Created Successfully!');
    }

    public function edit($id){
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

        $query = ExpenseType::where('id', $id)
            ->where('trash', 0);

        if ($user->role !== 'Admin') {

            if ($user->role === 'Manager') {
                $query->where('owner_id', $user->id);
            } elseif ($user->role === 'Employee') {
                $query->where('owner_id', $user->parent_id);
            }

            if (!empty($allowedCities)) {
                $query->whereIn('city_id', $allowedCities);
            } else {
                $query->whereRaw('1=0');
            }
        }

        $edit = $query->first();

        if (!$edit) {
            return redirect()->route('expense_type.index')
                ->with('error', 'Record not found or access denied!');
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
            ->select('expense_types.*', 'cities.name as city_name')
            ->latest()
            ->get();

        $cities = City::when($user->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('1=0');
            }
        })->get();

        return view('admin.expense_type.index', compact('expense_types', 'edit', 'cities'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'city_id' => 'nullable|integer',
        ]);

        $expense_type = ExpenseType::find($id);
        $expense_type->name = $request->name;
        $expense_type->city_id = $request->city_id;
        $expense_type->save();

        return redirect()->route('expense_type.index')->with('success', 'Expense Type Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();

        $query = ExpenseType::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $expense_type = $query->first();

        if (!$expense_type) {
            return redirect()->route('expense_type.index')
                ->with('error', 'Record not found!');
        }

        $exists = Expense::where('expense_type_id', $expense_type->id)->exists();

        if ($exists) {
            $expense_type->update(['trash' => 1]);

            return redirect()->route('expense_type.index')
                ->with('success', 'Expense Type soft-deleted successfully!');
        }

        $expense_type->delete();

        return redirect()->route('expense_type.index')
            ->with('success', 'Expense Type deleted successfully!');
    }

}
