<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'site_name',
        'hospital_name',
        'logo',
        'email',
        'phone',
        'address',
        'ntn',
        'pra',
        'secp',
        'bank_name',
        'bank_account_no',
        'bank_account_holder',
        'bank_qr_code',
        'phone_account_name',
        'phone_account_no',
        'phone_account_holder',
        'phone_qr_code',
        'tax',
        'salary_limit',
    ];
}
