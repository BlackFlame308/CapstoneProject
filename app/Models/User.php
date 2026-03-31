<?php

namespace App\Models;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'household_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasAnyRole($roles)
    {
        return $this->role && in_array($this->role->name, (array)$roles);
    }

    public function hasPermission($permissionName)
    {
        return $this->role && $this->role->hasPermission($permissionName);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin') || $this->hasRole('Captain');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}