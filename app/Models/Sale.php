<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_sale_id',
        'product_id',
        'quantity',
        'sale_price',
        'total',
        'status',
        'trash',
    ];

    public function totalSale(){
        return $this->belongsTo(TotalSale::class, 'total_sale_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

}
