<?php

namespace App\Http\Controllers;

use App\Models\WasteDisposal;
use App\Models\City;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WasteDisposalsController extends Controller
{
    public function index(){
        return view('admin.waste_disposals.index');
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

        return view('admin.waste_disposals.action', compact('cities'));
    }

    public function store(Request $request){
        $request->validate([
            'date' => 'required|date_format:d-m-Y',
            'city_id' => 'required|integer',
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
            'vehicle_no' => 'nullable|string',
        ]);

        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        WasteDisposal::create([
            'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            'city_id' => $request->city_id,
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
            'vehicle_no' => $request->vehicle_no,
            'owner_id' => $ownerId
        ]);

        return redirect()->route('waste-disposals.index')->with('success', 'Waste Disposals Created Successfully!');
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

        $query = WasteDisposal::where('trash', 0)->leftJoin('cities', 'waste_disposals.city_id', '=', 'cities.id')
        ->select('waste_disposals.*', 'cities.name as city_name');

        if ($authUser->role !== 'Admin') {
            $query->where('waste_disposals.owner_id', $ownerId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('waste_disposals.vehicle_no', 'like', "%$search%")
                    ->orWhere('waste_disposals.infections', 'like', "%$search%")
                    ->orWhere('waste_disposals.sharp', 'like', "%$search%")
                    ->orWhere('waste_disposals.total_weight', 'like', "%$search%")
                    ->orWhere('cities.name', 'like', "%$search%")
                    ->orWhereRaw("DATE_FORMAT(waste_disposals.date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }      

        $total = $query->count();

        $waste_disposals = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('waste_disposals.date','desc')
                    ->get();
        $data = [];
        foreach ($waste_disposals as $waste_disposal) {
            $action = '';
            if ($authUser->hasPermission('waste_disposals', 'edit')) {
                $action .= '
                    <a class="btn btn-primary btn-sm" href="'.route('waste-disposals.edit', $waste_disposal->id).'"><i class="fa-solid fa-pen-to-square"></i></a>
                ';
            }

            if ($authUser->hasPermission('waste_disposals', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete" data-action="'.route('waste-disposals.destroy', $waste_disposal->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }
            $data[] = [
                'date' => Carbon::parse($waste_disposal->date)->format('d-m-Y'),
                'vehicle_no' => $waste_disposal->vehicle_no ?? '-',
                'city_name' => $waste_disposal->city_name ?? '-',
                'infections' => ($waste_disposal->infections ?? 0) .' KG'. ' ('.($waste_disposal->infections_bags ?? 0) .')',
                'sharp' => ($waste_disposal->sharp ?? 0) .' KG'. ' ('.($waste_disposal->sharp_bags ?? 0) .')',
                'chemical' => ($waste_disposal->chemical ?? 0) .' KG'. ' ('.($waste_disposal->chemical_bags ?? 0) .')',
                'pharmaceutical' => ($waste_disposal->pharmaceutical ?? 0) .' KG'. ' ('.($waste_disposal->pharmaceutical_bags ?? 0) .')',
                'pathological' => ($waste_disposal->pathological ?? 0) .' KG'. ' ('.($waste_disposal->pathological_bags ?? 0) .')',
                'total_weight' => ($waste_disposal->total_weight ?? 0) . ' KG' . ' (' . ($waste_disposal->total_bags ?? 0) . ')',
                'action' => $action
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

        $edit = WasteDisposal::where('id', $id)
            ->when($user->role !== 'Admin', function ($q) use ($user) {
                if ($user->role === 'Manager') {
                    $q->where('owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('owner_id', $user->parent_id);
                }
            })
            ->first();

        if (!$edit) {
            return redirect()->route('waste-disposals.index')
                ->with('error', 'Record not found!');
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

        return view('admin.waste_disposals.action', compact('edit', 'cities'));
    }

    public function update(Request $request, $id){
        $waste = WasteDisposal::findOrFail($id);

        $request->validate([
            'date' => 'required|date_format:d-m-Y',
            'city_id' => 'required|integer',
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
            'vehicle_no' => 'nullable|string',
        ]);

        $waste->update([
            'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            'city_id' => $request->city_id,
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
            'vehicle_no' => $request->vehicle_no
        ]);

        return redirect()->route('waste-disposals.index')->with('success', 'Waste Disposals Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();

        $query = WasteDisposal::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $waste = $query->first();

        if (!$waste) {
            return redirect()->route('waste-disposals.index')
                ->with('error', 'Record not found!');
        }

        $waste->delete();

        return redirect()->route('waste-disposals.index')
            ->with('success', 'Waste Disposals Deleted Successfully!');
    }

}
