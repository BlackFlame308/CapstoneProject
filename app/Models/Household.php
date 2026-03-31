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
        'sitio',
        'purok',
        'emergency_contact',
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