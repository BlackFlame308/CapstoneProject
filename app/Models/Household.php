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
        'emergency_contact',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}