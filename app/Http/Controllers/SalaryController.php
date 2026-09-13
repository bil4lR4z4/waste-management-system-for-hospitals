<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index(){
        return view('admin.salary.index');
    }

    public function create(){
        $user = auth()->user();

        $employeesQuery = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->where('employees.status', 'active')
            ->select('employees.*', 'cities.short_name');

        if ($user->role === 'Manager') {
            $employeesQuery->where('employees.owner_id', $user->id);
        } elseif ($user->role === 'Employee') {
            $employeesQuery->where('employees.owner_id', $user->parent_id);
        }

        $employees = $employeesQuery->get();
        
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

        return view('admin.salary.action', compact('employees', 'cities'));
    }

    public function getEmployees(Request $request){
        $cityId = $request->city_id;

       $query = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->where('employees.status', 'active');

        if (!empty($cityId)) {
            $query->where('employees.city_id', $cityId);
        }

        $employees = $query
            ->select('employees.*', 'cities.short_name')
            ->get();
            
        $html = '<option value="">Select Employee</option>';

        foreach ($employees as $employee) {
            $html .= '<option 
                        value="'.$employee->id.'" 
                        data-salary="'.$employee->salary.'" 
                        data-allowance="'.$employee->allowance.'">
                        '.$employee->name.' ('.$employee->short_name.')
                    </option>';
        }

        return $html;
    }

    public function store(Request $request){
        $request->validate([
            'employee_id' => 'required|array',
            'amount' => 'nullable',
            'bonus' => 'nullable',
            'allowance' => 'nullable',
            'date' => 'required|date_format:d-m-Y',
        ]);

        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        foreach ($request->employee_id as $empId) {
            $employee = Employee::find($empId);
            $salaryAmount = $request->amount ?? $employee->salary ?? 0;
            $allowanceAmount = $request->allowance ?? $employee->allowance ?? 0;

            Salary::create([
                'employee_id' => $empId,
                'amount' => $salaryAmount,
                'bonus' => $request->bonus,
                'allowance' => $allowanceAmount,
                'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
                'owner_id' => $ownerId,
            ]);
        }

        return redirect()->route('salary.index')->with('success', 'Salary Created Successfully!');
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';
        $authUser = auth()->user(); 

        $query = Salary::Join('employees', 'salaries.employee_id', '=', 'employees.id')
        ->join('cities', 'employees.city_id', '=', 'cities.id')->where('salaries.trash', 0)
                    ->select(
                        'salaries.*',
                        'employees.name as employee_name',
                        'cities.short_name'
                    );

        if ($authUser->role === 'Manager') {
            $query->where('salaries.owner_id', $authUser->id);
        }
        elseif ($authUser->role === 'Employee') {
            $query->where('salaries.owner_id', $authUser->parent_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('salaries.amount', 'like', "%$search%")
                    ->orWhere('salaries.bonus', 'like', "%$search%")
                    ->orWhere('salaries.allowance', 'like', "%$search%")
                    ->orWhere('salaries.date', 'like', "%$search%")
                    ->orWhere('cities.short_name', 'like', "%$search%")
                    ->orWhere('employees.name', 'like', "%$search%")
                    ->orWhereRaw("DATE_FORMAT(salaries.date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }      

        $total = $query->count();

        $salaries = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('salaries.date','desc')
                    ->get();

        $data = [];
        foreach ($salaries as $salary) {
            $action = '';
             if ($authUser->hasPermission('salary', 'edit')) {
                $action .= '
                    <a class="btn btn-primary btn-sm" href="'.route('salary.edit', $salary->id).'"><i class="fa-solid fa-pen-to-square"></i></a>
                ';
            }

            if ($authUser->hasPermission('salary', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete" data-action="'.route('salary.destroy', $salary->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }

            $data[] = [
                'checkbox' => '<input type="checkbox" class="form-check-input shadow-none checkbox" value="'.$salary->id.'">',
                'date' => Carbon::parse($salary->date)->format('d-m-Y'),
                'employee_name' => $salary->employee_name . ' ('.$salary->short_name.')' ?? '-',
                'amount' => $salary->amount ?? '-',
                'bonus' => $salary->bonus ?? '-',
                'allowance' => $salary->allowance ?? '-',
                'total' => $salary->amount + $salary->bonus + $salary->allowance,
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
        if ($user->role === 'Admin') {
            $edit = Salary::where('id', $id)->first();
        } else {
            $edit = Salary::where('id', $id)
                ->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id)
                ->first();
        }

        if (!$edit) {
            return redirect()->route('salary.index')
                ->with('error', 'Record not found!');
        }
        
        $employeesQuery = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->where('employees.status', 'active')
            ->select('employees.*', 'cities.short_name');

        if ($user->role === 'Manager') {
            $employeesQuery->where('employees.owner_id', $user->id);
        } elseif ($user->role === 'Employee') {
            $employeesQuery->where('employees.owner_id', $user->parent_id);
        }

        $employees = $employeesQuery->get();
        return view('admin.salary.action', compact('employees', 'edit'));
    }
    
    public function multiple_edit(Request $request){
        $ids = explode(',', $request->salary_ids);
      
        $salaries = Salary::whereIn('id', $ids)->get();

        if ($salaries->isEmpty()) {
            return redirect()->route('salary.index')->with('error', 'No records found!');
        }

        $last = $salaries->last();
        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->select('employees.*', 'cities.short_name')
            ->get();

        return view('admin.salary.multiple_edit', compact('employees', 'salaries', 'last', 'ids'));
    }

    public function multiple_update(Request $request){
        $ids = explode(',', $request->salary_ids);

        Salary::whereIn('id', $ids)->update([
            'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            'amount' => $request->amount,
            'allowance' => $request->allowance,
            'bonus' => $request->bonus,
        ]);

        return redirect()->route('salary.index')
            ->with('success', 'Multiple Salaries Updated Successfully!');
    }

    public function update(Request $request, $id){
        $request->validate([
            'employee_id' => 'required',
            'amount' => 'required',
            'bonus' => 'nullable',
            'allowance' => 'nullable',
            'date' => 'required|date_format:d-m-Y',
        ]);

        $salary = Salary::find($id);
        $salary->employee_id = $request->employee_id;
        $salary->amount = $request->amount;
        $salary->bonus = $request->bonus;
        $salary->allowance = $request->allowance;
        $salary->date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
        $salary->save();

        return redirect()->route('salary.index')->with('success', 'Salary Updated Successfully!');
    }

    public function destroy($id){
        $authUser = auth()->user();
        $salaryQuery = Salary::where('id', $id);

        if ($authUser->role === 'Manager') {
            $salaryQuery->where('owner_id', $authUser->id);
        }elseif ($authUser->role === 'Employee') {
            $salaryQuery->where('owner_id', $authUser->parent_id);
        }

        $salary = $salaryQuery->first();
        if (!$salary) {
            return redirect()->route('salary.index')
                ->with('error', 'Record not found!');
        }

        $salary->trash = 1;
        $salary->save();

        return redirect()->route('salary.index')
            ->with('success', 'Salary deleted successfully!');
    }

}
