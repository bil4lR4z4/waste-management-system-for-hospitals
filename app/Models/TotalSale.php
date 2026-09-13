<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'sale_date',
        'note',
        'status',
        'trash',
        'owner_id',
        'hospital_id',
    ];

    public function sales(){
        return $this->hasMany(Sale::class, 'total_sale_id');
    }

    public function hospital(){
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
}
