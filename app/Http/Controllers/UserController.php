<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use App\Models\Hospital;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;


class UserController extends Controller
{
    public function index(){
        return view('admin.users.index');
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

        return view('admin.users.action', compact('cities'));
    }

    public function store(Request $request){
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'password'   => 'required|string|min:6',
            'plan_password'   => 'nullable|string|min:6',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city_id'    => 'required|array',
            'city_id.*'  => 'required|exists:cities,id',

            'ntn' => 'nullable|string', 
            'pra' => 'nullable|string', 
            'secp' => 'nullable|string', 
            'bank_name' => 'nullable|string', 
            'bank_account_no' => 'nullable|string', 
            'bank_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp', 
            'bank_account_holder' => 'nullable|string', 
            'phone_account_name' => 'nullable|string', 
            'phone_account_no' => 'nullable|string', 
            'phone_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp', 
            'phone_account_holder' => 'nullable|string',
        ]);

        $existing = User::where('email', $request->email)->first();

        $cityIds = implode(',', $request->city_id);
        $encrypted = Crypt::encryptString($request->password);

        $authUser = auth()->user();
        if($authUser->role === 'Admin'){
            $parentId = $authUser->id;
            $role = 'Manager';
        } elseif($authUser->role === 'Manager'){
            $parentId = $authUser->id;
            $role = 'Employee';
        } else {
            $parentId = $authUser->parent_id;
            $role = 'Employee';
        }

        if ($existing) {

            if ($existing->trash == 0) {
                return back()->with('error', 'Email already exists!');
            }

            $existing->update([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'password'   => Hash::make($request->password),
                'plan_password' => $encrypted,
                'city_ids'   => $cityIds,
                'phone' => $request->phone,
                'address' => $request->address,
                'ntn' => $request->ntn,
                'pra' => $request->pra,
                'secp' => $request->secp,
                'bank_name' => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'bank_qr_code' => $request->bank_qr_code,
                'bank_account_holder' => $request->bank_account_holder,
                'phone_account_name' => $request->phone_account_name,
                'phone_account_no' => $request->phone_account_no,
                'phone_qr_code' => $request->phone_qr_code,
                'phone_account_holder' => $request->phone_account_holder,
                'trash' => 0,
                'parent_id' => $parentId,
                'role' => $role,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Your account is restored successfully!');
        }

        $user = User::create([
            'first_name'     => $request->first_name,
            'last_name' => $request->last_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'plan_password' => $encrypted,
            'city_ids'    => $cityIds,
            'phone' => $request->phone,
            'address' => $request->address,
            'ntn' => $request->ntn,
            'pra' => $request->pra,
            'secp' => $request->secp,
            'bank_name' => $request->bank_name,
            'bank_account_no' => $request->bank_account_no,
            'bank_qr_code' => $request->bank_qr_code,
            'bank_account_holder' => $request->bank_account_holder,
            'phone_account_name' => $request->phone_account_name,
            'phone_account_no' => $request->phone_account_no,
            'phone_qr_code' => $request->phone_qr_code,
            'phone_account_holder' => $request->phone_account_holder,
            'role'     => $role,
            'parent_id'=> $parentId,
            'trash'    => 0,
        ]);

        $permissions = $request->permissions ?? [];
        foreach ($permissions as $page => $actions) {
            DB::table('user_permissions')->insert([
                'user_id'    => $user->id,
                'page'       => $page,
                'can_view'   => isset($actions['view']) ? 1 : 0,
                'can_insert' => isset($actions['insert']) ? 1 : 0,
                'can_edit'   => isset($actions['edit']) ? 1 : 0,
                'can_delete' => isset($actions['delete']) ? 1 : 0,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User registered successfully!');
    }

    public function ajax(Request $request){
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'] ?? '';

        $authUser = auth()->user();
        $query = User::query();
        $query->where('role', '!=', 'Admin')
          ->where('trash', 0);

        if ($authUser->role === 'Manager') {
            $query->where('parent_id', $authUser->id)->where('id', '!=', $authUser->id);
        } elseif ($authUser->role === 'Employee') {
            $query->where('parent_id', $authUser->parent_id)->where('id', '!=', $authUser->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        $users = $query->orderBy('id', 'desc')
                    ->offset($start)
                    ->limit($limit)
                    ->get();

        $data = [];
        foreach ($users as $user) {
            $action = '';
            if ($authUser->hasPermission('users', 'edit')) {
                $action .= '
                    <a href="'.route('users.edit', $user->id).'" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                ';
            }

            if ($authUser->hasPermission('users', 'delete')) {
                $action .= '
                    <button class="btn btn-danger btn-sm delete"
                        data-action="'.route('users.destroy', $user->id).'"
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
                'name' => $user->first_name . ' ' . $user->last_name,
                'email'      => $user->email,
                'action'     => $action
            ];
        }

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $total,
            "recordsFiltered" => $total,
            "data"            => $data
        ]);
    }

    public function edit($id){
        $edit = User::find($id);
        if(!$edit){
            return redirect()->route('hospital.index')->with('error', 'Record not found!');
        }
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
        return view('admin.users.action', compact('edit','cities'));
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,'.$user->id,
            'password'   => 'nullable|string|min:6',
            'plan_password'   => 'nullable|string|min:6',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city_id'    => 'required|array',
            'city_id.*'  => 'required|exists:cities,id',
            'ntn' => 'nullable|string',
            'pra' => 'nullable|string',
            'secp' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_account_no' => 'nullable|string',
            'bank_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'bank_account_holder' => 'nullable|string',
            'phone_account_name' => 'nullable|string',
            'phone_account_no' => 'nullable|string',
            'phone_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'phone_account_holder' => 'nullable|string',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city_ids'    => implode(',', $request->city_id),
            'ntn' => $request->ntn,
            'pra' => $request->pra,
            'secp' => $request->secp,
            'bank_name' => $request->bank_name,
            'bank_account_no' => $request->bank_account_no,
            'bank_account_holder' => $request->bank_account_holder,
            'phone_account_name' => $request->phone_account_name,
            'phone_account_no' => $request->phone_account_no,
            'phone_account_holder' => $request->phone_account_holder
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);

            $encrypted = Crypt::encryptString($request->password);
            
            $data['plan_password'] = $encrypted;
        }

        if ($request->hasFile('bank_qr_code')) {
            $data['bank_qr_code'] = uploadImage($request, 'bank_qr_code', 'user');
        }

        if ($request->hasFile('phone_qr_code')) {
            $data['phone_qr_code'] = uploadImage($request, 'phone_qr_code', 'user');
        }

        $user->update($data);

        $permissions = $request->permissions ?? [];

        foreach($permissions as $page => $actions){
            $permId = $actions['id'] ?? null;

            $data = [
                'can_view'   => isset($actions['view']) ? 1 : 0,
                'can_insert' => isset($actions['insert']) ? 1 : 0,
                'can_edit'   => isset($actions['edit']) ? 1 : 0,
                'can_delete' => isset($actions['delete']) ? 1 : 0,
                'updated_at' => now()
            ];

            if($permId){
                DB::table('user_permissions')->where('id', $permId)->update($data);
            } else { 
                $data['user_id'] = $user->id;
                $data['page'] = $page;
                $data['created_at'] = now();
                DB::table('user_permissions')->insert($data);
            }
        }


        return redirect()->back()->with('success', 'User Updated Successfully!');
    }

    public function destroy($id){
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'Record not found!');
        }

        $user->trash = 1;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User Deleted Successfully!');
    }

    public function login(Request $request){
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required|string|min:6',
        ]);

        $credentials = $request->only(['email', 'password']);
        $credentials['trash'] = 0;
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'Employee') {

                if (!$user->parent_id) {
                    Auth::logout();
                    return back()->with('error', 'Your manager account is not available.');
                }

                $parent = User::where('id', $user->parent_id)
                            ->where('trash', 0)
                            ->first();

                if (!$parent) {
                    Auth::logout();
                    return back()->with('error', 'Your manager account is blocked. You cannot login.');
                }
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withInput($request->only(['email', 'password']))
            ->withErrors(['email' => 'Invalid credentials'])
            ->with('error', 'Invalid credentials');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function profile($id){
        $edit = User::find($id);
        if(!$edit){
            return redirect()->route('dashboard')->with('error', 'Record not found!');
        }
        return view('admin.users.profile', compact('edit'));
    }

    public function updateProfile(Request $request, $id){
        $user = User::findOrFail($id);

        if ($user->role === 'Admin') {

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
                'email'      => 'required|email|unique:users,email,' . $user->id,

                'old_password' => 'nullable|string|min:6',
                'new_password' => 'nullable|string|min:6|same:password_confirmation',
                'password_confirmation' => 'nullable|string|min:6',
            ]);

            $data = [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
            ];

            if ($request->filled('new_password')) {

                if (!Hash::check($request->old_password, $user->password)) {
                    return back()->withErrors([
                        'old_password' => 'Current password is incorrect!',
                    ])->withInput();
                }

                if ($request->new_password !== $request->password_confirmation) {
                    return back()->with('error', 'New password confirmation does not match!');
                }

                $data['password'] = Hash::make($request->new_password);
            }

            $user->update($data);

            return redirect()->back()->with('success', 'Admin Updated Successfully!');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,'.$user->id,
    
            'old_password' => 'nullable|string|min:6',
            'new_password' => 'nullable|string|min:6|same:password_confirmation',
            'password_confirmation' => 'nullable|string|min:6',

            'plan_password'   => 'nullable|string|min:6',
            'phone' => 'required|string',
            'address' => 'required|string',
            'ntn' => 'nullable|string',
            'pra' => 'nullable|string',
            'secp' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_account_no' => 'nullable|string',
            'bank_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'bank_account_holder' => 'nullable|string',
            'phone_account_name' => 'nullable|string',
            'phone_account_no' => 'nullable|string',
            'phone_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'phone_account_holder' => 'nullable|string',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'ntn' => $request->ntn,
            'pra' => $request->pra,
            'secp' => $request->secp,
            'bank_name' => $request->bank_name,
            'bank_account_no' => $request->bank_account_no,
            'bank_account_holder' => $request->bank_account_holder,
            'phone_account_name' => $request->phone_account_name,
            'phone_account_no' => $request->phone_account_no,
            'phone_account_holder' => $request->phone_account_holder
        ];

        if ($request->filled('new_password')) {

            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors([
                    'old_password' => 'Current password is incorrect!',
                ])->withInput();
            }

            if ($request->new_password !== $request->password_confirmation) {
                return back()->with('error', 'New password confirmation does not match!');
            }

            $data['password'] = Hash::make($request->new_password);

            $encrypted = Crypt::encryptString($request->new_password);
            
            $data['plan_password'] = $encrypted;
        }

        if ($request->hasFile('bank_qr_code')) {
            $data['bank_qr_code'] = uploadImage($request, 'bank_qr_code', 'user');
        }

        if ($request->hasFile('phone_qr_code')) {
            $data['phone_qr_code'] = uploadImage($request, 'phone_qr_code', 'user');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User Updated Successfully!');
    }
}
