<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'name',
        'email',
        'employee_type',
        'phone_no',
        'cnic_no',
        'address',
        'salary',
        'allowance',
        'city_id',
        'employee_image',
        'gender',
        'date_of_birth',
        'security_no',
        'oldage_benefits',
        'license_no',
        'trash',
        'status',
        'reason',
        'bank_name',
        'account_no',
        'iban',
        'education',
        'owner_id',
    ];
}
