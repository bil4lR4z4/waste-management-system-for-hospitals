<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'receipt_no',
        'address',
        'city_id',
        'hospital_id',
        'total_bags',
        'infections',
        'infections_bags',
        'pathological',
        'pathological_bags',
        'chemical',
        'chemical_bags',
        'pharmaceutical',
        'pharmaceutical_bags',
        'sharp',
        'sharp_bags',
        'total_weight',
        'employee_id',
        'status',
        'trash',
        'owner_id',
    ];

    public function company()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
