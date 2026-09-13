<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'page', 'can_view', 'can_insert', 'can_edit', 'can_delete'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
