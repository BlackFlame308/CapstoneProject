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
        'headname',
        'contact_number',
        'emergency_contact',
        'region',
        'province',
        'city_mun',
        'barangay',
        'household_number',
    ];

    protected $appends = [
        'population',
        'vulnerable_count',
        'vulnerability_score',
        'vulnerability_badge',
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

    public function getVulnerabilityScoreAttribute()
    {
        return $this->members->reduce(function ($score, $member) {
            if ($member->is_pwd) {
                $score += 4;
            }

            if ($member->age >= 60) {
                $score += 2;
            }

            if ($member->age < 18) {
                $score += 1;
            }

            return $score;
        }, 0);
    }

    public function getVulnerabilityBadgeAttribute()
    {
        if ($this->vulnerability_score > 7) {
            return 'Critical';
        }

        if ($this->vulnerability_score > 4) {
            return 'High';
        }

        return 'Moderate';
    }
}
