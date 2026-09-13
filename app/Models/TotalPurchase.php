<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'purchase_date',
        'note',
        'status',
        'trash',
        'owner_id',
    ];

    public function purchases(){
        return $this->hasMany(Purchase::class, 'total_purchase_id');
    }
}
