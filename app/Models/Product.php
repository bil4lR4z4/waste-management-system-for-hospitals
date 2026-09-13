<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'product_mode',
        'price',
        'owner_id',
        'status',
        'trash',
    ];

    public function activePurchases(){
        return $this->hasMany(Purchase::class, 'product_id')
            ->whereHas('totalPurchase', function ($q) {
                $q->where('trash', 0);
            });
    }

    public function activeSales(){
        return $this->hasMany(Sale::class, 'product_id')
            ->whereHas('totalSale', function ($q) {
                $q->where('trash', 0);
            });
    }

}
