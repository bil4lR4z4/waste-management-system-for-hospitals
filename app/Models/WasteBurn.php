<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteBurn extends Model
{
    use HasFactory;

    protected $table = 'waste_burn'; 

    protected $fillable = [
        'date',
        'vehicle_no',
        'city_id',
        'infections',
        'infections_bags',
        'sharp',
        'sharp_bags',
        'pathological',
        'pathological_bags',
        'chemical',
        'chemical_bags',
        'pharmaceutical',
        'pharmaceutical_bags',
        'total_bags',
        'total_weight',
        'status',
        'trash',
        'owner_id',
    ];
}
