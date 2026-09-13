<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\TotalSale;
use App\Models\Hospital;
use Carbon\Carbon;

use DB;

class SaleController extends Controller
{
    public function index(){
        return view('admin.sale.index');
    }

    public function create(){
        $user = auth()->user();
        $productsQuery = Product::where('trash', 0)
            ->where('product_mode', 'sale')
            ->withSum('activePurchases as purchased_qty', 'quantity')
            ->withSum('activeSales as sold_qty', 'quantity');

        if ($user->role !== 'Admin') {
            $productsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $products = $productsQuery->get()
            ->map(function ($p) {
                $p->stock = ($p->purchased_qty ?? 0) - ($p->sold_qty ?? 0);
                return $p;
            });

        $hospitalsQuery = Hospital::where('trash', 0);
        if ($user->role !== 'Admin') {
            $hospitalsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $hospitals = $hospitalsQuery->get();

        return view('admin.sale.action', compact('products', 'hospitals'));
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

            'date'         => 'required|date_format:d-m-Y',
            'grand_total'  => 'required|numeric|min:0',
            'note'         => 'nullable|string',
            'hospital_id'  => 'required|exists:hospitals,id',
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
            $main = TotalSale::create([
                'total'         => $request->grand_total,
                'sale_date'=> Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
                'note'          => $request->note,
                'hospital_id'   => $request->hospital_id,
                'owner_id'      => $ownerId
            ]);

            foreach ($request->product_id as $i => $pid) {

                $price = $request->price[$i];
                if ($price <= 0) {
                    continue;
                }

                $qty   = $request->quantity[$i];
                $total = $qty * $price;

                Sale::create([
                    'total_sale_id'   => $main->id,
                    'product_id'    => $pid,
                    'quantity'      => $qty,
                    'sale_price'=> $price,
                    'total'         => $total,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Sale Created Successfully!');

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
        
        $query = TotalSale::with('sales.product', 'hospital')->where('trash', 0);
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
                    $qq->whereDate('sale_date', $date);
                })
                ->orWhere('total', 'like', "%{$search}%")
                ->orWhereHas('sales.product', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $total = $query->count();

        $sales_parent = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($sales_parent as $sale) {
            $action = '';
            if ($user->hasPermission('product_sale', 'edit')) {
                $action .='<a href="'. route('sale_product.edit', $sale->id) .'" class="btn btn-primary btn-sm me-1"><i class="fa-solid fa-pen-to-square"></i></a>';
            }
            if ($user->hasPermission('product_sale', 'delete')) {
                $action .='<button class="btn btn-danger btn-sm delete" data-action="'.route('sale_product.destroy', $sale->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash"></i></button>';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }

            $childData = [];
            foreach ($sale->sales as $p) {
                $childData[] = [
                    'name' => $p->product->name ?? '-',
                    'quantity' => $p->quantity,
                    'sale_price' => $p->sale_price,
                    'total' => $p->total
                ];
            }

            $data[] = [
                'id' => $sale->id,
                'sale_date' => $sale->sale_date ? Carbon::parse($sale->sale_date)->format('d-m-Y') : '-',
                'company_name' => $sale->hospital->company_name ?? '-',
                'total' => number_format($sale->total, 2),
                'action' => $action,
                'sales' => $childData 
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
        $query = TotalSale::with('sales.product')->where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $edit = $query->first();

        if (!$edit) {
            return back()->with('error', 'Record not found or access denied!');
        }
        $productsQuery = Product::where('trash', 0)
            ->where('product_mode', 'sale')
            ->withSum('activePurchases as purchased_qty', 'quantity')
            ->withSum('activeSales as sold_qty', 'quantity');

        if ($user->role !== 'Admin') {
            $productsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $products = $productsQuery->get()->map(function ($p) {
            $p->stock = ($p->purchased_qty ?? 0) - ($p->sold_qty ?? 0);
            return $p;
        });

        $hospitalsQuery = Hospital::where('trash', 0);
        if ($user->role !== 'Admin') {
            $hospitalsQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $hospitals = $hospitalsQuery->get();

        return view('admin.sale.action', compact('edit', 'products', 'hospitals'));
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

            'sale_id'  => 'nullable|array',
            'sale_id.*'=> 'nullable|numeric|exists:sales,id',

            'date'         => 'required|date_format:d-m-Y',
            'grand_total'  => 'required|numeric|min:0',
            'note'         => 'nullable|string',
            'hospital_id'  => 'required|exists:hospitals,id',
        ]);

        DB::beginTransaction();

        try {
            $main = TotalSale::findOrFail($id);

            $main->update([
                'total' => $request->grand_total,
                'sale_date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
                'note' => $request->note,
                'hospital_id' => $request->hospital_id
            ]);

            $existingIds = $main->sales()->pluck('id')->toArray();
            $frontendIds = $request->sale_id ?? [];

            $toDelete = array_diff($existingIds, $frontendIds);
            if (!empty($toDelete)) {
                $main->sales()->whereIn('id', $toDelete)->delete();
            }

            foreach ($request->product_id as $i => $pid) {
                $price = $request->price[$i];
                if ($price <= 0) continue;

                $qty = $request->quantity[$i];
                $total = $qty * $price;

                $saleId = $frontendIds[$i] ?? null;

                if ($saleId && in_array($saleId, $existingIds)) {
                    $main->sales()->where('id', $saleId)->update([
                        'product_id' => $pid,
                        'quantity' => $qty,
                        'sale_price' => $price,
                        'total' => $total,
                    ]);
                } else {
                    $main->sales()->create([
                        'product_id' => $pid,
                        'quantity' => $qty,
                        'sale_price' => $price,
                        'total' => $total,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sale_product.index')->with('success', 'Successfully Updated');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('sale_product.index')->with('error', $th->getMessage());
        }
    }

    public function destroy($id){
        $user = auth()->user();

        $query = TotalSale::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $sale = $query->first();
        if(!$sale){
            return back()->with('error', 'Record not found!');
        }
        $sale->trash = 1;
        $sale->save();
        return redirect()->route('sale_product.index')->with('success', 'Successfully Deleted');
    }
}
