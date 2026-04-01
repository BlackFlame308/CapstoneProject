<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'address',
        'purok',
        'headname',
        'contact_number',
        'emergency_contact',
        'region',
        'province',
        'city_mun',
        'barangay',
        'household_number',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function getPopulationAttribute()
    {
        return $this->members()->count();
    }

    public function getVulnerableCountAttribute()
    {
        return $this->members()->where(function ($query) {
            $query->where('is_pwd', true)
                ->orWhere('age', '<=', 17)
                ->orWhere('age', '>=', 60);
        })->count();
    }
}