<?php

namespace App\Http\Controllers;
use App\Models\Setting;
use DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(){
        $setting = Setting::first();
        $page = 'setting';
        return view('admin.setting.index', compact('setting', 'page'));
    }

    public function generalSetting(){
        $setting = Setting::first();
        $page = 'general_setting';
        return view('admin.setting.index', compact('setting', 'page'));
    }

    public function update(Request $request){
        $data = $request->validate([
            // 'site_name'      => 'nullable|string|max:255',
            'hospital_name' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'ntn' => 'nullable|string|max:50',
            'pra' => 'nullable|string|max:50',
            'secp' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:255',
            'bank_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'bank_account_holder' => 'nullable|string|max:255',

            'phone_account_name' => 'nullable|string|max:255',
            'phone_account_no' => 'nullable|string|max:255',
            'phone_qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'phone_account_holder' => 'nullable|string|max:255',
            'tax' => 'nullable|numeric|min:0',
            'salary_limit' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = uploadImage($request, 'logo', 'setting');
        }

        if ($request->hasFile('bank_qr_code')) {
            $data['bank_qr_code'] = uploadImage($request, 'bank_qr_code', 'setting');
        }

        if ($request->hasFile('phone_qr_code')) {
            $data['phone_qr_code'] = uploadImage($request, 'phone_qr_code', 'setting');
        }

        $setting = Setting::first();

        if ($setting) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }

        if($request->page == 'general_setting'){
            return redirect()
                ->route('general_setting.index')
                ->with('success', 'General Setting Updated Successfully!');
        } else {
            return redirect()
                ->route('setting.index')
                ->with('success', 'Setting Updated Successfully!');
        }
    }

    public function settingPdf(){
        $setting = Setting::first();

        $pdf = Pdf::loadView('admin.setting.setting_pdf', [
            'setting' => $setting
        ]);

        $pdf->setPaper('A4', 'landscape'); 

        $filename = 'company-profile-' . now()->format('d-m-Y_H-i') . '.pdf';
        return $pdf->download($filename);
    }

}
