<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalRent extends Model
{
    use HasFactory;
    protected $fillable = [
        'hospital_id',
        'start_date',
        'end_date',
        'total',
        'note',
        'status',
        'trash',
    ];

    public function rents(){
        return $this->hasMany(Rent::class, 'total_rent_id');
    }

    public function hospital(){
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
}
