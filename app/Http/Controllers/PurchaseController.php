<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\TotalPurchase;
use Carbon\Carbon;
use DB;
class PurchaseController extends Controller{
    public function index(){
        return view('admin.purchase.index');
    }

    public function create(){
        $user = auth()->user();
        $query = Product::where('trash', 0);
        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $products = $query->get();
        return view('admin.purchase.action', compact('products'));
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
            $main = TotalPurchase::create([
                'total'         => $request->grand_total,
                'purchase_date'=> Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
                'note'          => $request->note,
                'owner_id'      => $ownerId
            ]);

            foreach ($request->product_id as $i => $pid) {

                $price = $request->price[$i];
                if ($price <= 0) {
                    continue;
                }

                $qty   = $request->quantity[$i];
                $total = $qty * $price;

                Purchase::create([
                    'total_purchase_id'   => $main->id,
                    'product_id'    => $pid,
                    'quantity'      => $qty,
                    'purchase_price'=> $price,
                    'total'         => $total,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Purchase Created Successfully!');

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

        $query = TotalPurchase::with('purchases.product')->where('trash', 0);
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
                    $qq->whereDate('purchase_date', $date);
                })
                ->orWhere('total', 'like', "%{$search}%")
                ->orWhereHas('purchases.product', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $total = $query->count();

        $purchases_parent = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($purchases_parent as $purchase) {
            $action = '';
            if ($user->hasPermission('product_purchase', 'edit')) {
                $action .='<a href="'. route('purchase_product.edit', $purchase->id) .'" class="btn btn-primary btn-sm me-1"><i class="fa-solid fa-pen-to-square"></i></a>';
            }
            if ($user->hasPermission('product_purchase', 'delete')) {
                $action .='<button class="btn btn-danger btn-sm delete" data-action="'.route('purchase_product.destroy', $purchase->id).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash"></i></button>';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }

            $childData = [];
            foreach ($purchase->purchases as $p) {
                $childData[] = [
                    'name' => $p->product->name ?? '-',
                    'quantity' => $p->quantity,
                    'purchase_price' => $p->purchase_price,
                    'total' => $p->total
                ];
            }

            $data[] = [
                'id' => $purchase->id,
                'purchase_date' => $purchase->purchase_date ? Carbon::parse($purchase->purchase_date)->format('d-m-Y') : '-',
                'total' => number_format($purchase->total, 2),
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
        $query = TotalPurchase::with('purchases.product')->where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $edit = $query->first();
        if (!$edit) {
            return back()->with('error', 'Record not found or access denied!');
        }

        $productQuery = Product::where('trash', 0);
        if ($user->role !== 'Admin') {
            $productQuery->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $products = $productQuery->get();
        return view('admin.purchase.action', compact('edit', 'products'));
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

            'purchase_id'  => 'nullable|array',
            'purchase_id.*'=> 'nullable|numeric|exists:purchases,id',

            'date'         => 'required|date_format:d-m-Y',
            'grand_total'  => 'required|numeric|min:0',
            'note'         => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $main = TotalPurchase::findOrFail($id);

            $main->update([
                'total' => $request->grand_total,
                'purchase_date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
                'note' => $request->note,
            ]);

            $existingIds = $main->purchases()->pluck('id')->toArray();
            $frontendIds = $request->purchase_id ?? [];

            $toDelete = array_diff($existingIds, $frontendIds);
            if (!empty($toDelete)) {
                $main->purchases()->whereIn('id', $toDelete)->delete();
            }

            foreach ($request->product_id as $i => $pid) {
                $price = $request->price[$i];
                if ($price <= 0) continue;

                $qty = $request->quantity[$i];
                $total = $qty * $price;

                $purchaseId = $frontendIds[$i] ?? null;

                if ($purchaseId && in_array($purchaseId, $existingIds)) {
                    $main->purchases()->where('id', $purchaseId)->update([
                        'product_id' => $pid,
                        'quantity' => $qty,
                        'purchase_price' => $price,
                        'total' => $total,
                    ]);
                } else {
                    $main->purchases()->create([
                        'product_id' => $pid,
                        'quantity' => $qty,
                        'purchase_price' => $price,
                        'total' => $total,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('purchase_product.index')->with('success', 'Successfully Updated');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('purchase_product.index')->with('error', $th->getMessage());
        }
    }

    public function destroy($id){
        $user = auth()->user();
        $query = TotalPurchase::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $purchase = $query->first();
        if (!$purchase) {
            return back()->with('error', 'Record not found or access denied!');
        }

        $purchase->trash = 1;
        $purchase->save();
        return redirect()->route('purchase_product.index')->with('success', 'Successfully Deleted');
    }
}
