<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\City;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
class EmployeeController extends Controller
{
    public function index(){
        return view('admin.employee.index');
    }

    public function view($id){
        $employee = Employee::leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->where('employees.id', $id)
            ->select(
                'employees.*',
                'cities.name as city_name'
            )
            ->firstOrFail();

        return view('admin.employee.view', compact('employee'));
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';

        $authUser = auth()->user(); 


        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $query = Employee::leftJoin('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->select(
                'employees.*',
                'cities.short_name',
                'cities.name as city_name'
            );
        if ($authUser->role !== 'Admin') {
            $query->where('employees.owner_id', $ownerId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('employees.name', 'like', "%$search%")
                ->orWhere('employees.phone_no', 'like', "%$search%")
                ->orWhere('employees.cnic_no', 'like', "%$search%")
                ->orWhere('employees.address', 'like', "%$search%")
                ->orWhere('employees.salary', 'like', "%$search%")
                ->orWhere('employees.gender', 'like', "%$search%")
                ->orWhere('cities.name', 'like', "%$search%");
            });
        }

        $total = $query->count();

        $employees = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('employees.id','desc')
                    ->get();

        
        $data = [];
        foreach ($employees as $employee) {
            $image = asset('public/images/User.png');
            if ($employee->employee_image) {
                $image = asset('public/'.$employee->employee_image);
            }
            $action = '';
            if ($authUser->hasPermission('employee', 'edit')) {
                $action .= '
                    <a class="btn btn-primary btn-sm" href="'.route('employee.edit', $employee->id).'"><i class="fa-solid fa-pen-to-square"></i></a> 
                ';
            }

            if ($authUser->hasPermission('employee', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete" data-action="'.route('employee.destroy', $employee->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }

            $info = "<div class='d-flex gap-3 align-items-center'>
                        <div>
                            <img src='$image' width='50' height='50' class='rounded-circle border'>
                        </div>
                        <div>
                            <p class='mb-0'>".($employee->name ?? '')." (".($employee->short_name ?? '').")</p>
                            <p class='mb-0 small'>".$employee->email ?? ''."</p>
                        </div>
                    </div>";

            $status = "<span class='badge bg-success'>Active</span>";
            if($employee->status == 'inactive'){
                $status = "<span class='badge bg-danger'>Inactive</span>";
            }
            $view= route('employee.view', $employee->id);

            $data[] = [
                'info'    => $info,
                'phone_no'        => $employee->phone_no,
                'cnic_no'       => $employee->cnic_no,
                'gender'     => "<span class='text-capitalize'>".$employee->gender ?? '-'."</span>",
                'salary' => $employee->salary ?? '-',
                'allowance' => $employee->allowance ?? '-',
                'status' => $status,
                'action'      => "<a href='{$view}' class='btn btn-info btn-sm'><i class='fa-solid fa-eye'></i></a>" . $action
            ];
        }

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }

    public function create(){
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
        return view('admin.employee.action', compact('cities'));
    }

    public function store(Request $request){
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone_no'       => 'nullable|string|max:20',
            'cnic_no'        => 'required|string|max:20',
            'address'        => 'nullable|string',
            'salary'         => 'required|numeric|min:0',
            'allowance'      => 'nullable|numeric|min:0',
            'city_id'     => 'nullable|integer',
            'gender'         => 'nullable|in:male,female,other',
            'employee_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'email'          => 'nullable|email',
            'oldage_benefits' => 'nullable|string',
            'security_no' => 'nullable|string',
            'license_no' => 'nullable|string',
            'employee_type' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'reason' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_no' => 'nullable|string',
            'iban' => 'nullable|string',
            'education' => 'nullable|string',
        ]);

        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $existing = Employee::where('cnic_no', $request->cnic_no)->first();
        if ($existing) {

            if ($existing->trash == 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Employee already exists.');
            }

            if ($existing->trash == 1) {
                $existing->update([
                    'trash' => 0,
                    'owner_id' => $ownerId
                ]);

                return redirect()->route('employee.index')
                    ->with('success', 'Employee restored successfully.');
            }
        }
        if ($request->hasFile('employee_image')) {
            $data['employee_image'] = uploadImage($request, 'employee_image', 'employee');
        }

        $data['trash'] = 0; 
        $data['owner_id'] = $ownerId;

        Employee::create($data);

        return redirect()->route('employee.index')
            ->with('success', 'Employee added successfully');
    }

    public function destroy($id){
        $authUser = auth()->user();
        $query = Employee::where('id', $id)
                        ->where('trash', 0);

        if ($authUser->role === 'Manager') {
            $query->where('owner_id', $authUser->id);
        }elseif ($authUser->role === 'Employee') {
            $query->where('owner_id', $authUser->parent_id);
        }
        
        $employee = $query->first();

        if (!$employee) {
            return back()->with('error', 'Record not found or access denied!');
        }

        $employee->update([
            'trash' => 1
        ]);

        return back()->with('success', 'Employee removed successfully!');
    }

    public function edit($id){
        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $query = Employee::where('id', $id);

        if ($authUser->role !== 'Admin') {
            $query->where('owner_id', $ownerId);
        }

        $edit = $query->firstOrFail();

        $allowedCities = [];

        if ($authUser->role === 'Manager' && $authUser->city_ids) {
            $allowedCities = array_filter(explode(',', $authUser->city_ids));
        } elseif ($authUser->role === 'Employee' && $authUser->parent_id) {
            $parent = User::find($authUser->parent_id);
            if ($parent && $parent->city_ids) {
                $allowedCities = array_filter(explode(',', $parent->city_ids));
            }
        }

        $cities = City::when($authUser->role !== 'Admin', function ($q) use ($allowedCities) {
            if (!empty($allowedCities)) {
                $q->whereIn('id', $allowedCities);
            } else {
                $q->whereRaw('0 = 1');
            }
        })->get();
        return view('admin.employee.action', compact('edit', 'cities'));
    }

    public function update(Request $request, $id){
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone_no'       => 'nullable|string|max:20',
            'cnic_no'        => 'required|string|max:20',
            'address'        => 'nullable|string',
            'salary'         => 'required|numeric|min:0',
            'allowance'      => 'nullable|numeric|min:0',
            'city_id'     => 'nullable|integer',
            'gender'         => 'nullable|in:male,female,other',
            'employee_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'email'          => 'nullable|email',
            'oldage_benefits' => 'nullable|string',
            'security_no' => 'nullable|string',
            'license_no' => 'nullable|string',
            'employee_type' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'reason' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_no' => 'nullable|string',
            'iban' => 'nullable|string',
            'education' => 'nullable|string',
        ]);

        $existingCNIC = Employee::where('cnic_no', $request->cnic_no)
            ->where('id', '!=', $employee->id)
            ->first();

        if ($existingCNIC) {
            if ($existingCNIC->trash == 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Employee with this CNIC already exists.');
            }

            if ($existingCNIC->trash == 1) {
                $existingCNIC->update([
                    'trash' => 0,
                    'owner_id' => auth()->id()
                ]);

                return redirect()->route('employee.index')
                    ->with('success', 'Existing employee restored successfully.');
            }
        }

        if ($request->hasFile('employee_image')) {
            $data['employee_image'] = uploadImage($request, 'employee_image', 'employee');
        }

        $employee->update($data);

        return redirect()->route('employee.index')
            ->with('success', 'Employee updated successfully');
    }

    public function pdf($id){
        $setting = Setting::first();
        $employee = Employee::leftJoin('cities', 'cities.id', '=', 'employees.city_id')->where('employees.id', $id)->select('employees.*', 'cities.name as city')->first();

        $pdf = Pdf::loadView('admin.employee.employee_pdf', [
            'setting' => $setting,
            'employee' => $employee
        ]);

        $pdf->setPaper('A4', 'portrait'); 

        $filename = 'employee-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }
 
}
