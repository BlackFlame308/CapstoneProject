<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'name',
        'age',
        'gender',
        'special_needs',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }
}