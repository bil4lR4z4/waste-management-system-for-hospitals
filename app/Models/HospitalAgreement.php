<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'start_date',
        'end_date',
        'number_of_beds',
        'waste_generation_volume',
        'waste_type',
        'preferred_waste_collection_frequency',
        'has_temporary_waste_storage',
        'waste_storage_method',
        'date',
        'status',
        'document',
        'notes',
        'trash',
    ];

    // Relation with Hospital
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
