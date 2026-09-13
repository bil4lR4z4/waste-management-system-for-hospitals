<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name', 
        'last_name',
        'email',
        'password',
        'plan_password',
        'city_ids',
        'phone',
        'address',
        'ntn',
        'pra',
        'secp',
        'bank_name',
        'bank_account_no',
        'bank_qr_code',
        'bank_account_holder',
        'phone_account_name',
        'phone_account_no',
        'phone_qr_code',
        'phone_account_holder',
        'role',
        'parent_id',
        'trash'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function permissions(){
        return $this->hasMany(UserPermission::class);
    }

    public function hasPermission($page, $action){
        if ($this->role === 'Admin') {
            return true;
        }

        $perm = $this->permissions()->where('page', $page)->first();
        if(!$perm) return false;

        return match($action){
            'view' => $perm->can_view,
            'insert' => $perm->can_insert,
            'edit'   => $perm->can_edit,
            'delete' => $perm->can_delete,
            default  => false,
        };
    }

}
