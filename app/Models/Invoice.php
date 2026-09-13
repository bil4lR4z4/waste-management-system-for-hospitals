<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'receipt_no',
        'amount',
        'hospital_id',
        'employee_id',
        'tax',
        'tax_status',
        'owner_id'
    ];

    // 🔗 Relationships (optional)
    public function company()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function hospital(){
        return $this->belongsTo(Hospital::class);
    }

}
