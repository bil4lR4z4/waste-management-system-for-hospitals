<?php

namespace App\Http\Controllers;

use App\Models\WasteBurn;
use App\Models\City;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WasteBurnController extends Controller
{
    public function index(){
        return view('admin.waste_burn.index');
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

        return view('admin.waste_burn.action', compact('cities'));
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

        WasteBurn::create([
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

        return redirect()->route('waste-burn.index')->with('success', 'Waste Burn Created Successfully!');
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

        $query = WasteBurn::where('trash', 0)->leftJoin('cities', 'waste_burn.city_id', '=', 'cities.id')
        ->select('waste_burn.*', 'cities.name as city_name');

        if ($authUser->role !== 'Admin') {
            $query->where('waste_burn.owner_id', $ownerId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('waste_burn.vehicle_no', 'like', "%$search%")
                    ->orWhere('waste_burn.infections', 'like', "%$search%")
                    ->orWhere('waste_burn.sharp', 'like', "%$search%")
                    ->orWhere('waste_burn.total_weight', 'like', "%$search%")
                    ->orWhere('cities.name', 'like', "%$search%")
                    ->orWhereRaw("DATE_FORMAT(waste_burn.date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }      

        $total = $query->count();

        $waste_burns = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('waste_burn.date','desc')
                    ->get();
        $data = [];
        foreach ($waste_burns as $waste_burn) {
            $action = '';
            if ($authUser->hasPermission('waste_burn', 'edit')) {
                $action .= '
                    <a class="btn btn-primary btn-sm" href="'.route('waste-burn.edit', $waste_burn->id).'"><i class="fa-solid fa-pen-to-square"></i></a>
                ';
            }

            if ($authUser->hasPermission('waste_burn', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete" data-action="'.route('waste-burn.destroy', $waste_burn->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i></button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }
            $data[] = [
                'date' => Carbon::parse($waste_burn->date)->format('d-m-Y'),
                'vehicle_no' => $waste_burn->vehicle_no ?? '-',
                'city_name' => $waste_burn->city_name ?? '-',
                'infections' => ($waste_burn->infections ?? 0) .' KG'. ' ('.($waste_burn->infections_bags ?? 0) .')',
                'sharp' => ($waste_burn->sharp ?? 0) .' KG'. ' ('.($waste_burn->sharp_bags ?? 0) .')',
                'chemical' => ($waste_burn->chemical ?? 0) .' KG'. ' ('.($waste_burn->chemical_bags ?? 0) .')',
                'pharmaceutical' => ($waste_burn->pharmaceutical ?? 0) .' KG'. ' ('.($waste_burn->pharmaceutical_bags ?? 0) .')',
                'pathological' => ($waste_burn->pathological ?? 0) .' KG'. ' ('.($waste_burn->pathological_bags ?? 0) .')',
                'total_weight' => ($waste_burn->total_weight ?? 0) . ' KG' . ' (' . ($waste_burn->total_bags ?? 0) . ')',
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

        $edit = WasteBurn::where('id', $id)
            ->when($user->role !== 'Admin', function ($q) use ($user) {
                if ($user->role === 'Manager') {
                    $q->where('owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('owner_id', $user->parent_id);
                }
            })
            ->first();

        if (!$edit) {
            return redirect()->route('waste-burn.index')
                ->with('error', 'Unauthorized or Record not found!');
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

        return view('admin.waste_burn.action', compact('edit', 'cities'));
    }

    public function update(Request $request, $id){
        $waste = WasteBurn::findOrFail($id);

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

        return redirect()->route('waste-burn.index')->with('success', 'Waste Burn Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();

        $waste = WasteBurn::where('id', $id)
            ->when($user->role !== 'Admin', function ($q) use ($user) {
                if ($user->role === 'Manager') {
                    $q->where('owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('owner_id', $user->parent_id);
                }
            })
            ->first();

        if (!$waste) {
            return redirect()->route('waste-burn.index')
                ->with('error', 'Record not found!');
        }

        $waste->delete();

        return redirect()->route('waste-burn.index')
            ->with('success', 'Waste Burn Deleted Successfully!');
    }

}
