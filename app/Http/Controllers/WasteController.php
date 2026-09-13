<?php

namespace App\Http\Controllers;
use App\Models\Waste;
use App\Models\Hospital;
use App\Models\Employee;
use App\Models\City;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WasteController extends Controller{
    
    public function index(){
        return view('admin.waste.index');
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

        $reciept_no = Waste::max('receipt_no') + 1;

        $oldCityId = old('city_id');
        $hospitals = [];
        $employees = [];

        if($oldCityId) {
            $city = City::with([
                'hospitals' => function($q) {
                    $q->where('trash', 0);
                },
                'employees' => function($q) {
                    $q->where('trash', 0)->where('status', 'active');
                }
            ])->find($oldCityId);

            if($city) {
                $hospitals = $city->hospitals;
                $employees = $city->employees;
            }
        }

        return view('admin.waste.action', compact('hospitals', 'employees', 'reciept_no', 'cities'));
    }

    public function store(Request $request){
        $request->validate([
            'date' => 'required|date_format:d-m-Y',
            'receipt_no' => 'required',
            'city_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'infections' => 'nullable|numeric|min:0',
            'infections_bags' => 'nullable|integer',
            'sharp' => 'nullable|numeric|min:0',
            'sharp_bags' => 'nullable|integer',
            'pharmaceutical' => 'nullable|numeric|min:0',
            'pharmaceutical_bags' => 'nullable|integer',
            'chemical' => 'nullable|numeric|min:0',
            'chemical_bags' => 'nullable|integer',
            'pathological' => 'nullable|numeric|min:0',
            'pathological_bags' => 'nullable|integer',
            'total_bags' => 'required|numeric|min:0',
            'total_weight' => 'required|numeric|min:0',
            'employee_id' => 'required|integer',
            'address' => 'nullable|string',
        ]);
        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        Waste::create([
            'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            'receipt_no' => $request->receipt_no,
            'city_id' => $request->city_id,
            'hospital_id' => $request->hospital_id,
            'infections' => $request->infections,
            'infections_bags' => $request->infections_bags,
            'sharp' => $request->sharp,
            'sharp_bags' => $request->sharp_bags,
            'pharmaceutical' => $request->pharmaceutical,
            'pharmaceutical_bags' => $request->pharmaceutical_bags,
            'chemical' => $request->chemical,
            'chemical_bags' => $request->chemical_bags,
            'pathological' => $request->pathological,
            'pathological_bags' => $request->pathological_bags,
            'total_weight' => $request->total_weight,
            'total_bags' => $request->total_bags,
            'employee_id' => $request->employee_id,
            'address' => $request->address,
            'owner_id' => $ownerId
        ]);

        return redirect()->route('waste.index')->with('success', 'Waste Created Successfully!');
    }

    public function view($id){
        $waste = Waste::with(['hospital','employee'])
            ->where('wastes.trash', 0)
            ->where('wastes.id', $id)
            ->firstOrFail();

        return view('admin.waste.view', compact('waste'));
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';
        $authUser = auth()->user(); 

        $query = Waste::join('hospitals', 'wastes.hospital_id', '=', 'hospitals.id')->where('hospitals.trash', 0)
                    ->select(
                        'hospitals.company_name',
                        'wastes.*'
                    );
        
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        if ($authUser->role !== 'Admin') {
            $query->where('wastes.owner_id', $ownerId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('hospitals.company_name', 'like', "%$search%")
                    ->orWhere('wastes.receipt_no', 'like', "%$search%")
                    ->orWhere('wastes.infections', 'like', "%$search%")
                    ->orWhere('wastes.sharp', 'like', "%$search%")
                    ->orWhere('wastes.pharmaceutical', 'like', "%$search%")
                    ->orWhere('wastes.chemical', 'like', "%$search%")
                    ->orWhere('wastes.pathological', 'like', "%$search%")
                    ->orWhere('wastes.total_weight', 'like', "%$search%")
                    ->orWhereRaw("DATE_FORMAT(wastes.date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }      

        $total = $query->count();

        $wastes = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('wastes.date','desc')
                    ->get();
        $data = [];
        foreach ($wastes as $waste) {
            $action = '';
            if ($authUser->hasPermission('waste', 'edit')) {
                $action .= '
                    <a class="btn btn-primary btn-sm" href="'.route('waste.edit', $waste->id).'"><i class="fa-solid fa-pen-to-square"></i></a>
                ';
            }

            if ($authUser->hasPermission('waste', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete" data-action="'.route('waste.destroy', $waste->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }

            $view = route('waste.view', $waste->id);
            $data[] = [
                'date' => Carbon::parse($waste->date)->format('d-m-Y'),
                'company_name' => $waste->company_name ?? '-',
                'receipt_no' => $waste->receipt_no ?? '-',
                'infections' => ($waste->infections ?? 0) .' KG'. ' ('.($waste->infections_bags ?? 0) .')',
                'sharp' => ($waste->sharp ?? 0) .' KG'. ' ('.($waste->sharp_bags ?? 0) .')',
                'chemical' => ($waste->chemical ?? 0) .' KG'. ' ('.($waste->chemical_bags ?? 0) .')',
                'pharmaceutical' => ($waste->pharmaceutical ?? 0) .' KG'. ' ('.($waste->pharmaceutical_bags ?? 0) .')',
                'pathological' => ($waste->pathological ?? 0) .' KG'. ' ('.($waste->pathological_bags ?? 0) .')',
                'total_weight' => ($waste->total_weight ?? 0) . ' KG' . ' (' . ($waste->total_bags ?? 0) . ')',
                'action' => "<a href='{$view}' class='btn btn-info btn-sm'><i class='fa-solid fa-eye'></i></a>" . $action
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

        $query = Waste::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $edit = $query->first();

        if (!$edit) {
            return redirect()->route('waste.index')->with('error', 'Record not found or access denied!');
        }

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

        $hospitals = Hospital::join('cities', 'hospitals.city_id', '=', 'cities.id')
            ->where('hospitals.city_id', $edit->city_id)
            ->where('hospitals.trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user) {
                $q->where('hospitals.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
            })
            ->select('hospitals.*', 'cities.short_name')
            ->get();

        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.city_id', $edit->city_id)
            ->where('employees.trash', 0)
            ->where('employees.status', 'active')
            ->when($user->role !== 'Admin', function ($q) use ($user) {
                $q->where('employees.owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
            })
            ->select('employees.*', 'cities.short_name')
            ->get();

        return view('admin.waste.action', compact(
            'hospitals',
            'edit',
            'employees',
            'cities'
        ));
    }

    public function update(Request $request, $id){
        $waste = Waste::findOrFail($id);

        $request->validate([
            'date' => 'required|date_format:d-m-Y',
            'receipt_no' => 'required',
            'city_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'infections' => 'nullable|numeric|min:0',
            'infections_bags' => 'nullable|integer',
            'sharp' => 'nullable|numeric|min:0',
            'sharp_bags' => 'nullable|integer',
            'pharmaceutical' => 'nullable|numeric|min:0',
            'pharmaceutical_bags' => 'nullable|integer',
            'chemical' => 'nullable|numeric|min:0',
            'chemical_bags' => 'nullable|integer',
            'pathological' => 'nullable|numeric|min:0',
            'pathological_bags' => 'nullable|integer',
            'total_weight' => 'required|numeric|min:0',
            'total_bags' => 'required|numeric|min:0',
            'employee_id' => 'required|integer',
            'address' => 'nullable|string',
        ]);

        $waste->update([
            'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            'receipt_no' => $request->receipt_no,
            'city_id' => $request->city_id,
            'hospital_id' => $request->hospital_id,
            'infections' => $request->infections,
            'infections_bags' => $request->infections_bags,
            'sharp' => $request->sharp,
            'sharp_bags' => $request->sharp_bags,
            'pharmaceutical' => $request->pharmaceutical,
            'pharmaceutical_bags' => $request->pharmaceutical_bags,
            'chemical' => $request->chemical,
            'chemical_bags' => $request->chemical_bags,
            'pathological' => $request->pathological,
            'pathological_bags' => $request->pathological_bags,
            'total_weight' => $request->total_weight,
            'total_bags' => $request->total_bags,
            'employee_id' => $request->employee_id,
            'address' => $request->address
        ]);

        return redirect()->route('waste.index')->with('success', 'Waste Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();

        $query = Waste::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $waste = $query->first();

        if (!$waste) {
            return redirect()->route('waste.index')
                ->with('error', 'Record not found!');
        }

        $waste->delete();

        return redirect()->route('waste.index')
            ->with('success', 'Waste Deleted Successfully!');
    }

    public function pdf($id){
        $setting = Setting::first();

        $waste = Waste::with(['employee', 'hospital'])
            ->leftJoin('cities', 'cities.id', '=', 'wastes.city_id')
            ->select('wastes.*', 'cities.name as city')
            ->where('wastes.id', $id)
            ->firstOrFail();

        $pdf = Pdf::loadView('admin.waste.waste_pdf', compact('setting','waste'));

        $pdf->setPaper('A4', 'portrait');

        $filename = 'waste-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

}
