<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(){
        return view('admin.product.index');
    }

    public function create(){
        return view('admin.product.action');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'quantity' => 'required',
            'product_mode' => 'required',
            'price' => 'nullable|numeric|min:0|required_if:product_mode,sale',
        ]);
    
        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $product = Product::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'product_mode' => $request->product_mode,
            'price' => $request->price ?? 0,
            'owner_id' => $ownerId
        ]);

        return redirect()->back()->with('success', 'Product Created Successfully!');
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';
        $user = auth()->user();

        $query = Product::query()->where('trash', 0);
        if (auth()->user()->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('quantity', 'like', "%{$search}%")
                ->orWhere('price', 'like', "%{$search}%")
                ->orWhere('product_mode', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        $products = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $authUser = auth()->user(); 
        $data = [];
        foreach ($products as $product) {
            $action = '';
            if ($authUser->hasPermission('product', 'edit')) {
                $action .= '
                     <a class="btn btn-primary btn-sm" href="'.route('product.edit', $product->id).'">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a> 
                ';
            }

            if ($authUser->hasPermission('product', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete"
                        data-action="'.route('product.destroy', $product->id).'"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmDeleteModal">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }
            $productMode = "<span class='badge text-bg-dark text-capitalize'>{$product->product_mode}</span>";
            if ($product->product_mode == 'rent') {
                $productMode = "<span class='badge text-bg-info text-capitalize'>{$product->product_mode}</span>";
            }
            $data[] = [
                'name' => $product->name ?? '-',
                'quantity' => $product->quantity ?? '-',
                'price' => number_format($product->price, 2),
                'product_mode' => $productMode,
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
        $query = Product::where('id', $id);
        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }
        $edit = $query->first();
        if (!$edit) {
            return redirect()->route('product.index')
                            ->with('error', 'Record not found or access denied!');
        }
        return view('admin.product.action', compact('edit'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'quantity' => 'required',
            'product_mode' => 'required',
            'price' => 'required|numeric|min:0',
        ]);
        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'product_mode' => $request->product_mode,
            'price' => $request->price,
        ]);

        return redirect()->back()->with('success', 'Product Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();

        $query = Product::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $product = $query->first();

        if (!$product) {
            return redirect()->route('product.index')
                ->with('error', 'Record not found!');
        }

        $product->update(['trash' => 1]);

        return redirect()->route('product.index')
            ->with('success', 'Product Deleted Successfully!');
    }
}
