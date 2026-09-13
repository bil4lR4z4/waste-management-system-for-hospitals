<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Hospital;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(){
        return view('admin.invoice.index');
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

        $hospitals = Hospital::join('cities', 'hospitals.city_id', '=', 'cities.id')
            ->where('hospitals.trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user, $allowedCities) {
                if ($user->role === 'Manager') {
                    $q->where('hospitals.owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('hospitals.owner_id', $user->parent_id);
                }

                if (!empty($allowedCities)) {
                    $q->whereIn('hospitals.city_id', $allowedCities);
                } else {
                    $q->whereRaw('1=0'); 
                }
            })
            ->select('hospitals.*', 'cities.short_name')
            ->get();

        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user, $allowedCities) {
                if ($user->role === 'Manager') {
                    $q->where('employees.owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('employees.owner_id', $user->parent_id);
                }

                if (!empty($allowedCities)) {
                    $q->whereIn('employees.city_id', $allowedCities);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->select('employees.*', 'cities.short_name')
            ->get();

        $reciept_no = Invoice::max('receipt_no') + 1;

        return view('admin.invoice.action', compact('hospitals', 'employees', 'reciept_no'));
    }

    public function store(Request $request){
        $request->validate([
            'date' => 'required|date_format:d-m-Y',
            'receipt_no' => 'required|unique:invoices,receipt_no',
            'hospital_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'employee_id' => 'required|integer',
            'tax_status' => 'nullable',
        ]);

        $authUser = auth()->user(); 
        if ($authUser->role === 'Manager') {
            $ownerId = $authUser->id;
        } elseif ($authUser->role === 'Employee') {
            $ownerId = $authUser->parent_id;
        } else {
            $ownerId = null;
        }

        $setting = Setting::first();
        $tax = 0;
        if($request->tax_status == 1){
            $tax = $request->amount * $setting->tax / 100;
        }

        $invoice = Invoice::create([
            'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            'receipt_no' => $request->receipt_no,
            'hospital_id' => $request->hospital_id,
            'amount' => $request->amount,
            'employee_id' => $request->employee_id,
            'tax_status' => $request->tax_status,
            'tax' => $tax,
            'owner_id' => $ownerId
        ]);

        $setting = Setting::first();

        if ($request->has('print')) {
            return view('admin.invoice.print', compact('invoice', 'setting'));
        }

        return redirect()->back()->with('success', 'Invoice Created Successfully!');
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
        $query = Invoice::join('hospitals', 'invoices.hospital_id', '=', 'hospitals.id')
            ->join('cities as hc', 'hospitals.city_id', '=', 'hc.id')
            ->join('employees', 'employees.id', '=', 'invoices.employee_id')
            ->join('cities as ec', 'employees.city_id', '=', 'ec.id')
            ->where('hospitals.trash', 0)
            ->select(
                'hospitals.company_name',
                'employees.name as employee_name',
                'hc.short_name as hospital_city',
                'ec.short_name as employee_city',
                'invoices.*'
            );

        if (auth()->user()->role !== 'Admin') {
            $query->where('invoices.owner_id', $ownerId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('hospitals.company_name', 'like', "%{$search}%")
                ->orWhere('invoices.receipt_no', 'like', "%{$search}%")
                ->orWhere('invoices.amount', 'like', "%{$search}%")
                ->orWhere('employees.name', 'like', "%{$search}%")
                ->orWhereRaw("DATE_FORMAT(invoices.date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        $total = $query->count();

        $invoices = $query->offset($start)
            ->limit($limit)
            ->orderBy('invoices.id', 'desc')
            ->get();

        $authUser = auth()->user(); 
        $data = [];
        foreach ($invoices as $invoice) {
            $action = '';
            if ($authUser->hasPermission('invoice', 'edit')) {
                $action .= '
                     <a class="btn btn-primary btn-sm" href="'.route('invoice.edit', $invoice->id).'">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a> 
                ';
            }

            if ($authUser->hasPermission('invoice', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete"
                        data-action="'.route('invoice.destroy', $invoice->id).'"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmDeleteModal">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                ';
            }

            if ($action == '') {
                $action = '<span class="text-muted">Access Denied</span>';
            }
            $data[] = [
                'date' => Carbon::parse($invoice->date)->format('d-m-Y'),
                'company_name' => $invoice->company_name . ' (' . $invoice->hospital_city . ')',
                'employee_name' => $invoice->employee_name . ' (' . $invoice->employee_city . ')',
                'receipt_no' => $invoice->receipt_no ?? '-',
                'amount' => number_format($invoice->amount),
                'action' => "<a class='btn btn-info btn-sm' href='" . route('invoice.view', $invoice->id) . "'><i class='fa-solid fa-eye'></i></a>'" . $action
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

        $query = Invoice::where('id', $id);
        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $edit = $query->first();
        if (!$edit) {
            return redirect()->route('invoice.index')
                ->with('error', 'Record not found or access denied!');
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

        $hospitals = Hospital::join('cities', 'hospitals.city_id', '=', 'cities.id')
            ->where('hospitals.trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user, $allowedCities) {
                if($user->role === 'Manager'){
                    $q->where('hospitals.owner_id', $user->id);
                }elseif($user->role === 'Employee'){
                    $q->where('hospitals.owner_id', $user->parent_id);
                }

                if (!empty($allowedCities)) {
                    $q->whereIn('hospitals.city_id', $allowedCities);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->select('hospitals.*', 'cities.short_name')
            ->get();

        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->where('employees.trash', 0)
            ->when($user->role !== 'Admin', function ($q) use ($user, $allowedCities) {
                if ($user->role === 'Manager') {
                    $q->where('employees.owner_id', $user->id);
                } elseif ($user->role === 'Employee') {
                    $q->where('employees.owner_id', $user->parent_id);
                }

                if (!empty($allowedCities)) {
                    $q->whereIn('employees.city_id', $allowedCities);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->select('employees.*', 'cities.short_name')
            ->get();

        return view('admin.invoice.action', compact('hospitals', 'edit', 'employees'));
    }

    public function update(Request $request, $id){
        $invoice = Invoice::with(['hospital','employee'])->findOrFail($id);

        $request->validate([
            'date' => 'required|date_format:d-m-Y',
            'receipt_no' => 'required|unique:invoices,receipt_no,' . $invoice->id,
            'hospital_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'employee_id' => 'nullable|integer',
            'tax_status' => 'nullable',
        ]);

        $setting = Setting::first();
        $tax = 0;
        if($request->tax_status == 1){
            $tax = $request->amount * $setting->tax / 100;
        }
        
        $invoice->update([
            'date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            'receipt_no' => $request->receipt_no,
            'hospital_id' => $request->hospital_id,
            'amount' => $request->amount,
            'employee_id' => $request->employee_id,
            'tax_status' => $request->tax_status,
            'tax' => $tax,
        ]);

        $setting = Setting::first();

        if ($request->has('print')) {
            return view('admin.invoice.print', compact('invoice', 'setting'));
        }

        return redirect()->back()->with('success', 'Invoice Updated Successfully!');
    }

    public function destroy($id){
        $user = auth()->user();

        $query = Invoice::where('id', $id);

        if ($user->role !== 'Admin') {
            $query->where('owner_id', $user->role === 'Manager' ? $user->id : $user->parent_id);
        }

        $invoice = $query->first();

        if (!$invoice) {
            return redirect()->route('invoice.index')
                ->with('error', 'Record not found!');
        }

        $invoice->update(['trash' => 1]);

        return redirect()->route('invoice.index')
            ->with('success', 'Invoice Deleted Successfully!');
    }

    public function view($id){
        $invoice = Invoice::find($id);
        if(empty($invoice)){
            return back()->with('error', 'Record not found!');
        }

        $setting = Setting::first();
        return view('admin.invoice.view', compact('invoice', 'setting'));
    }

    public function print($id){
        $invoice = Invoice::find($id);
        if(empty($invoice)){
            return back()->with('error', 'Record not found!');
        }

        $setting = Setting::first();
        return view('admin.invoice.print', compact('invoice', 'setting'));
    }
}