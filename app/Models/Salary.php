<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $table = 'salaries';

    protected $fillable = [
        'employee_id',
        'amount',
        'allowance',
        'bonus',
        'date',
        'status',
        'trash',
        'owner_id',
    ];
}
