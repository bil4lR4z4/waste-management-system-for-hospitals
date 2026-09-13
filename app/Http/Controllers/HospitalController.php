<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class HospitalController extends Controller{
    public function index(){
        return view('admin.hospital.index');
    }

    public function create(){
        $user = auth()->user();

        $citiesQuery = City::query();

        if ($user->role !== 'Admin') {
            if ($user->role === 'Employee') {
                $parent = User::find($user->parent_id);
                $assignedCityIds = !empty($parent->city_ids) ? array_filter(explode(',', $parent->city_ids)) : [];
            } else {
                $assignedCityIds = !empty($user->city_ids) ? array_filter(explode(',', $user->city_ids)) : [];
            }

            if (count($assignedCityIds)) {
                $citiesQuery->whereIn('id', $assignedCityIds);
            } else {
                $citiesQuery->whereRaw('0 = 1');
            }
        }

        $cities = $citiesQuery->get();

        $lastReg = Hospital::max('registration_number'); 
        $newReg  = $lastReg ? $lastReg + 1 : 1;

        return view('admin.hospital.action', compact('cities', 'newReg'));
    }

    public function store(Request $request){
        $request->validate([
            'company_name' => 'required',
            'owner_name' => 'required',
            'facilities' => 'nullable|array', 
            'address' => 'required',
            'registration_number' => 'required|unique:hospitals,registration_number',
            'city_id' => 'required',
            'contact_number' => 'required',
            'number_of_beds' => 'nullable|integer|min:0',
            'waste_generation_volume' => 'nullable|string|max:255',
            'waste_type' => 'nullable|array',
            'preferred_waste_collection_frequency' => 'nullable|string|max:255',
            'has_temporary_waste_storage' => 'nullable|in:yes,no',
            'waste_storage_method' => 'nullable|array',
            'date' => 'nullable',
            'expiry_date' => 'nullable',
            'ntn' => 'nullable',
            'phc' => 'nullable',
            'pra' => 'nullable',
            'secp' => 'nullable',
            'email' => 'required|email|unique:hospitals,email',
            'payment' => 'nullable|numeric|min:0',
        ], [
            'company_name.required' => 'Please enter the hospital name.',
        ]);

        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $hospital = new Hospital();
        $hospital->company_name = $request->company_name;
        $hospital->owner_name = $request->owner_name;
        $hospital->facilities = implode(',', (array) $request->facilities);
        $hospital->address = $request->address;
        $hospital->registration_number = $request->registration_number;
        $hospital->ntn = $request->ntn;
        $hospital->phc = $request->phc;
        $hospital->pra = $request->pra;
        $hospital->secp = $request->secp;
        $hospital->email = $request->email;
        $hospital->city_id = $request->city_id;
        $hospital->contact_number = $request->contact_number;
        $hospital->payment = $request->payment;
        $hospital->number_of_beds = $request->number_of_beds;
        $hospital->waste_generation_volume = $request->waste_generation_volume;
        $hospital->waste_type = implode(',', (array) $request->waste_type);
        $hospital->preferred_waste_collection_frequency = $request->preferred_waste_collection_frequency;
        $hospital->has_temporary_waste_storage = $request->has_temporary_waste_storage ?? 'no';
        $hospital->waste_storage_method = implode(',',(array) $request->waste_storage_method);
        $hospital->date = $request->date
            ? Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d')
            : null;
        $hospital->expiry_date = $request->expiry_date
            ? Carbon::createFromFormat('d-m-Y', $request->expiry_date)->format('Y-m-d')
            : null;
        $hospital->owner_id = $ownerId;
        $hospital->save();

        return redirect()->route('hospital.index')->with('success', 'Hospital Created Successfully!');
    }

    public function view($id){
        $company = Hospital::where('hospitals.trash', 0)
                    ->where('hospitals.id', $id)
                    ->select(
                        'hospitals.*',
                    )->firstOrFail();

        return view('admin.hospital.view', compact('company'));
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';
        $authUser = auth()->user(); 

        $query = Hospital::where('hospitals.trash', 0)
                    ->select(
                        'hospitals.*',
                    );

        if ($authUser->role === 'Manager') {
            $query->where('hospitals.owner_id', $authUser->id);
        }
        elseif ($authUser->role === 'Employee') {
            $query->where('hospitals.owner_id', $authUser->parent_id);
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('hospitals.company_name', 'like', "%$search%")
                ->orWhere('hospitals.registration_number', 'like', "%$search%")
                ->orWhere('hospitals.contact_number', 'like', "%$search%")
                ->orWhere('hospitals.owner_name', 'like', "%$search%")
                ->orWhere('hospitals.address', 'like', "%$search%")
                ->orWhereHas('city', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                });
            });
        }

        $total = $query->count();

        $hospitals = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('hospitals.id','desc')
                    ->get();

        $data = [];
        foreach ($hospitals as $hospital) {
            $action = '';
            if ($authUser->hasPermission('company', 'edit')) {
                $action .= '
                    <a class="btn btn-primary btn-sm" href="'.route('hospital.edit', $hospital->id).'"><i class="fa-solid fa-pen-to-square"></i></a>
                ';
            }

            if ($authUser->hasPermission('company', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete" data-action="'.route('hospital.destroy', $hospital->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }

            $today = Carbon::today();

            if (!empty($hospital->expiry_date) && $today->gt($hospital->expiry_date)) {
                $status = '<span class="badge bg-danger">Expired</span>';
            } else {
                $status = '<span class="badge bg-success">Active</span>';
            }

            $view = route('hospital.view', $hospital->id);
            $data[] = [
                'company_name' => $hospital->company_name ?? '-',
                'owner_name' => $hospital->owner_name ?? '-',
                'registration_number' => $hospital->registration_number ?? '-',
                'phone' => $hospital->contact_number ?? '-',
                'city' => $hospital->city->name ?? '-',
                'status' => $status,
                'action' => '<a class="btn btn-info btn-sm" href="'.$view.'"><i class="fa-solid fa-eye"></i></a>' .$action,
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
        $hospitalQuery = Hospital::where('id', $id);

        if ($user->role !== 'Admin') {
            $hospitalQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $edit = $hospitalQuery->first();

        if (!$edit) {
            return redirect()->route('hospital.index')
                ->with('error', 'Record not found or access denied!');
        }

        $citiesQuery = City::query();

        if ($user->role === 'Admin') {
            $cities = $citiesQuery->get();
        } else {
            $assignedCityIds = [];

            if ($user->role === 'Employee' && $user->parent_id) {
                $parent = User::find($user->parent_id);
                if (!empty($parent->city_ids)) {
                    $assignedCityIds = array_filter(explode(',', $parent->city_ids));
                }
            } elseif ($user->role === 'Manager') {
                if (!empty($user->city_ids)) {
                    $assignedCityIds = array_filter(explode(',', $user->city_ids));
                }
            }

            if (count($assignedCityIds)) {
                $citiesQuery->whereIn('id', $assignedCityIds);
            } else {
                $citiesQuery->whereRaw('0 = 1');
            }

            $cities = $citiesQuery->get();
        }

        return view('admin.hospital.action', compact('cities', 'edit'));
    }

    public function update(Request $request, $id){
        $hospital = Hospital::findOrFail($id);

        $request->validate([
            'company_name' => 'required',
            'owner_name' => 'required',
            'facilities' => 'nullable|array', 
            'address' => 'required',
            'registration_number' => [
                'required',
                Rule::unique('hospitals')->ignore($hospital->id),
            ],
            'city_id' => 'required',
            'contact_number' => 'required',
            'number_of_beds' => 'nullable|integer|min:0',
            'waste_generation_volume' => 'nullable|string|max:255',
            'waste_type' => 'nullable|array',
            'preferred_waste_collection_frequency' => 'nullable|string|max:255',
            'has_temporary_waste_storage' => 'nullable|in:yes,no',
            'waste_storage_method' => 'nullable|array',
            'date' => 'nullable',
            'expiry_date' => 'nullable',
            'ntn' => 'nullable',
            'phc' => 'nullable',
            'pra' => 'nullable',
            'secp' => 'nullable',
            'email' => 'required|email|unique:hospitals,email,'.$hospital->id,
            'payment' => 'nullable|numeric|min:0',
        ], [
            'company_name.required' => 'Please enter the hospital name.',
        ]);

        $hospital->company_name = $request->company_name;
        $hospital->owner_name = $request->owner_name;
        $hospital->facilities = implode(',', (array) $request->facilities);
        $hospital->address = $request->address;
        $hospital->registration_number = $request->registration_number;
        $hospital->ntn = $request->ntn;
        $hospital->phc = $request->phc;
        $hospital->pra = $request->pra;
        $hospital->secp = $request->secp;
        $hospital->email = $request->email;
        $hospital->city_id = $request->city_id;
        $hospital->contact_number = $request->contact_number;
        $hospital->payment = $request->payment;
        $hospital->number_of_beds = $request->number_of_beds;
        $hospital->waste_generation_volume = $request->waste_generation_volume;
        $hospital->waste_type = implode(',', (array) $request->waste_type);
        $hospital->preferred_waste_collection_frequency = $request->preferred_waste_collection_frequency;
        $hospital->has_temporary_waste_storage = $request->has_temporary_waste_storage ?? 'no';
        $hospital->waste_storage_method = implode(',', (array) $request->waste_storage_method);
        $hospital->date = $request->date
            ? Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d')
            : null;
        $hospital->expiry_date = $request->expiry_date
            ? Carbon::createFromFormat('d-m-Y', $request->expiry_date)->format('Y-m-d')
            : null;
        $hospital->save();

        return redirect()->route('hospital.index')->with('success', 'Hospital Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();
        $hospitalQuery = Hospital::where('id', $id);

        if ($user->role !== 'Admin') {
            $hospitalQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $hospital = $hospitalQuery->first();
        if(!$hospital){
            return redirect()->route('hospital.index')->with('error', 'Record not found!');
        }
        $hospital->trash = 1;
        $hospital->save();

        return redirect()->route('hospital.index')->with('success', 'Hospital Deleted Successfully!');
    }

    public function pdf($id){
        $setting = Setting::first();
        $hospital = Hospital::leftJoin('cities', 'cities.id', '=', 'hospitals.city_id')->where('hospitals.id', $id)->select('hospitals.*', 'cities.name as city')->first();

        $pdf = Pdf::loadView('admin.hospital.hospital_pdf', [
            'setting' => $setting,
            'hospital' => $hospital
        ]);

        $pdf->setPaper('A4', 'portrait'); 

        $filename = 'company-profile-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

}
