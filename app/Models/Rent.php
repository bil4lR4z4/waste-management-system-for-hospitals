<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_rent_id',
        'product_id',
        'rent_price',
        'quantity',
        'total',
        'end_date',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function totalRent(){
        return $this->belongsTo(TotalRent::class, 'total_rent_id');
    }
}
