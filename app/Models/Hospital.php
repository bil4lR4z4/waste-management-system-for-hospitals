<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'owner_name',
        'email',
        'facilities',
        'registration_number',
        'ntn',
        'phcb',
        'pra',
        'secp',
        'payment',
        'address',
        'city_id',
        'contact_number',
        'number_of_beds',
        'waste_generation_volume',
        'waste_type',
        'preferred_waste_collection_frequency',
        'has_temporary_waste_storage',
        'waste_storage_method',
        'date',
        'expiry_date',
        'status',
        'trash',
        'owner_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
