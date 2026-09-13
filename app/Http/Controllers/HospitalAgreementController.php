<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HospitalAgreement;
use App\Models\Hospital;
use Illuminate\Validation\Rule;

class HospitalAgreementController extends Controller
{
    public function index(){
        return view('admin.hospital_agreement.index');
    }

    public function create(){
        $hospitals = Hospital::where('trash', 0)->get();
        return view('admin.hospital_agreement.action', compact('hospitals'));
    }

    public function store(Request $request){
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_beds' => 'nullable|integer|min:0',
            'waste_generation_volume' => 'nullable|string|max:255',
            'waste_type' => 'nullable|string|max:255',
            'preferred_waste_collection_frequency' => 'nullable|string|max:255',
            'has_temporary_waste_storage' => 'nullable|in:yes,no',
            'waste_storage_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'hospital_id.required' => 'Please select a hospital.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'has_temporary_waste_storage.in' => 'Please select yes or no for temporary waste storage.',
        ]);

        $agreement = new HospitalAgreement();
        $agreement->hospital_id = $request->hospital_id;
        $agreement->start_date = $request->start_date;
        $agreement->end_date = $request->end_date;
        $agreement->number_of_beds = $request->number_of_beds;
        $agreement->waste_generation_volume = $request->waste_generation_volume;
        $agreement->waste_type = $request->waste_type;
        $agreement->preferred_waste_collection_frequency = $request->preferred_waste_collection_frequency;
        $agreement->has_temporary_waste_storage = $request->has_temporary_waste_storage ?? 'no';
        $agreement->waste_storage_method = $request->waste_storage_method;
        $agreement->notes = $request->notes;

        $agreement->save();

        return redirect()->route('hospital_agreements.index')->with('success', 'Agreement Created Successfully!');
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';

        $query = Hospital::where('hospitals.trash', 0)
                    ->select(
                        'hospitals.*',
                    );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('hospitals.company_name', 'like', "%$search%")
                    ->orWhere('hospitals.registration_number', 'like', "%$search%")
                    ->orWhere('hospitals.contact_number', 'like', "%$search%")
                    ->orWhere('hospitals.owner_name', 'like', "%$search%")
                    ->orWhere('hospitals.address', 'like', "%$search%");
            });
        }      

        $total = $query->count();

        $hospitals = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('hospitals.id','desc')
                    ->get();

        $data = [];
        foreach ($hospitals as $hospital) {
            $data[] = [
                'company_name' => $hospital->company_name ?? '-',
                'owner_name' => $hospital->owner_name ?? '-',
                'registration_number' => $hospital->registration_number ?? '-',
                'phone' => $hospital->contact_number ?? '-',
                'city' => $hospital->city->name ?? '-',
                'action' => '<a class="btn btn-primary btn-sm" href="'.route('hospital.edit', $hospital->id).'"><i class="fa-solid fa-pen-to-square"></i></a> <button class="btn btn-danger btn-sm delete" data-action="'.route('hospital.destroy', $hospital->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>'
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
        $edit = Hospital::find($id);
        if(!$edit){
            return redirect()->route('hospital.index')->with('error', 'Record not found!');
        }
        $cities = City::all();
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
        ], [
            'company_name.required' => 'Please enter the hospital name.',
        ]);

        $hospital->company_name = $request->company_name;
        $hospital->owner_name = $request->owner_name;
        $hospital->facilities = implode(',', $request->facilities);
        $hospital->address = $request->address;
        $hospital->registration_number = $request->registration_number;
        $hospital->city_id = $request->city_id;
        $hospital->contact_number = $request->contact_number;
        $hospital->save();

        return redirect()->route('hospital.index')->with('success', 'Hospital Updated Successfully!');
    }

    public function destroy($id){
        $hospital = Hospital::find($id);
        if(!$hospital){
            return redirect()->route('hospital.index')->with('error', 'Record not found!');
        }
        $hospital->trash = 1;
        $hospital->save();

        return redirect()->route('hospital.index')->with('success', 'Hospital Deleted Successfully!');
    }


}
