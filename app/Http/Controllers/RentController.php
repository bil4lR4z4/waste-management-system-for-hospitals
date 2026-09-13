<?php

namespace App\Http\Controllers;
use App\Models\Rent;
use App\Models\Product;
use App\Models\TotalRent;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class RentController extends Controller
{
    public function index(){
        return view('admin.rent.index');
    }

    public function create(){
        $user = auth()->user();
        $productsQuery = Product::where('trash', 0)
            ->where('product_mode', 'rent');

        if ($user->role !== 'Admin') {
            $productsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $products = $productsQuery->get();

        $hospitalsQuery = Hospital::where('trash', 0);
        if ($user->role !== 'Admin') {
            $hospitalsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $hospitals = $hospitalsQuery->get();

        return view('admin.rent.action', compact('products', 'hospitals'));
    }

    public function store(Request $request){
        $request->validate([
            'product_id'   => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',

            'quantity'     => 'required|array',
            'quantity.*'   => 'required|numeric|min:1',

            'price'        => 'required|array',
            'price.*'      => 'required|numeric|min:0',

            'total'        => 'required|array',
            'total.*'      => 'required|numeric|min:0',

            'start_date'         => 'required|date_format:d-m-Y',
            'end_date'           => 'required|date_format:d-m-Y',
            'hospital_id'         => 'required|exists:hospitals,id',
            'grand_total'  => 'required|numeric|min:0',
            'note'         => 'nullable|string',
        ]);

        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        DB::beginTransaction();

        try {
            $main = TotalRent::create([
                'total'         => $request->grand_total,
                'hospital_id'   => $request->hospital_id,
                'start_date'=> Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d'),
                'end_date'=> Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d'),
                'note'          => $request->note,
                'owner_id'      => $ownerId,
            ]);

            foreach ($request->product_id as $i => $pid) {

                $price = $request->price[$i];
                if ($price <= 0) {
                    continue;
                }

                $qty   = $request->quantity[$i];
                $total = $qty * $price;

                Rent::create([
                    'total_rent_id'   => $main->id,
                    'product_id'    => $pid,
                    'quantity'      => $qty,
                    'rent_price'=> $price,
                    'total'         => $total,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Rent Created Successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';
        $user   = auth()->user();

        $query = TotalRent::with('rents.product', 'hospital')->where('trash', 0);
        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $date = null;
                try {
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
                        $date = Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                    }
                } catch (\Exception $e) {}
                $q->when($date, function ($qq) use ($date) {
                    $qq->whereDate('start_date', $date)
                    ->orWhereDate('end_date', $date);
                })
                ->orWhereHas('hospital', function ($q2) use ($search) {
                    $q2->where('company_name', 'like', "%{$search}%");
                })

                ->orWhere('total', 'like', "%{$search}%")
                ->orWhereHas('rents.product', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $total = $query->count();

        $rents_parent = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($rents_parent as $rent) {

            $action = '';
            if ($user->hasPermission('product_rent', 'edit')) {
                $action .='<a href="'. route('rent_product.edit', $rent->id) .'" class="btn btn-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>';
            }
            if ($user->hasPermission('product_rent', 'delete')) {
                $action .='<button class="btn btn-danger btn-sm delete" data-action="'.route('rent_product.destroy', $rent->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash"></i></button>';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }

            $childData = [];
            foreach ($rent->rents as $p) {
                $childData[] = [
                    'name' => $p->product->name ?? '-',
                    'quantity' => $p->quantity,
                    'rent_price' => $p->rent_price,
                    'total' => $p->total
                ];
            }

            $data[] = [
                'id' => $rent->id,
                'start_date' => $rent->start_date ? Carbon::parse($rent->start_date)->format('d-m-Y') : '-',
                'end_date' => $rent->end_date ? Carbon::parse($rent->end_date)->format('d-m-Y') : '-',
                'company_name' => $rent->hospital->company_name ?? '-', 
                'total' => number_format($rent->total, 2),
                'action' => $action,
                'purchases' => $childData 
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
        $query = TotalRent::with('rents.product')->where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $edit = $query->first();

        if (!$edit) {
            return back()->with('error', 'Record not found or access denied!');
        }
        $productsQuery = Product::where('trash', 0)
            ->where('product_mode', 'rent');

        if ($user->role !== 'Admin') {
            $productsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $products = $productsQuery->get();
        $hospitalsQuery = Hospital::where('trash', 0);
        if ($user->role !== 'Admin') {
            $hospitalsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $hospitals = $hospitalsQuery->get();

        return view('admin.rent.action', compact('edit', 'products', 'hospitals'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'product_id'   => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',

            'quantity'     => 'required|array',
            'quantity.*'   => 'required|numeric|min:1',

            'price'        => 'required|array',
            'price.*'      => 'required|numeric|min:0',

            'total'        => 'required|array',
            'total.*'      => 'required|numeric|min:0',

            'rent_id'  => 'nullable|array',
            'rent_id.*'=> 'nullable|numeric|exists:rents,id',

            'start_date'         => 'required|date_format:d-m-Y',
            'end_date'           => 'required|date_format:d-m-Y',
            'hospital_id'         => 'required|exists:hospitals,id',
            'grand_total'  => 'required|numeric|min:0',
            'note'         => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $main = TotalRent::findOrFail($id);

            $main->update([
                'total' => $request->grand_total,
                'hospital_id'   => $request->hospital_id,
                'start_date'=> Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d'),
                'end_date'=> Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d'),
                'note' => $request->note,
            ]);

            $existingIds = $main->rents()->pluck('id')->toArray();
            $frontendIds = $request->rent_id ?? [];

            $toDelete = array_diff($existingIds, $frontendIds);
            if (!empty($toDelete)) {
                $main->rents()->whereIn('id', $toDelete)->delete();
            }

            foreach ($request->product_id as $i => $pid) {
                $price = $request->price[$i];
                if ($price <= 0) continue;

                $qty = $request->quantity[$i];
                $total = $qty * $price;

                $rentId = $frontendIds[$i] ?? null;

                if ($rentId && in_array($rentId, $existingIds)) {
                    $main->rents()->where('id', $rentId)->update([
                        'product_id' => $pid,
                        'quantity' => $qty,
                        'rent_price' => $price,
                        'total' => $total,
                    ]);
                } else {
                    $main->rents()->create([
                        'product_id' => $pid,
                        'quantity' => $qty,
                        'rent_price' => $price,
                        'total' => $total,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('rent_product.index')->with('success', 'Successfully Updated');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('rent_product.index')->with('error', $th->getMessage());
        }
    }

    public function destroy($id){
        $user = auth()->user();
        $query = TotalRent::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $rent = $query->first();

        if (!$rent) {
            return back()->with('error', 'Record not found!');
        }

        $rent->trash = 1;
        $rent->save();

        return redirect()->route('rent_product.index')
                        ->with('success', 'Rent record deleted successfully!');
    }

}
