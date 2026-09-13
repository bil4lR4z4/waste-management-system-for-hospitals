<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_purchase_id',
        'product_id',
        'quantity',
        'purchase_price',
        'total',
        'status',
        'trash',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function totalPurchase(){
        return $this->belongsTo(TotalPurchase::class, 'total_purchase_id');
    }


}
