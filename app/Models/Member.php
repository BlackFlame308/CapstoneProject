<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birth_date',
        'birth_place',
        'sex',
        'civil_status',
        'religion',
        'citizenship',
        'profession',
        'contact_number',
        'email',
        'education_level',
        'is_graduate',
        'is_pwd',
        'age',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_graduate' => 'boolean',
        'is_pwd' => 'boolean',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' .
            ($this->middle_name ? $this->middle_name . ' ' : '') .
            $this->last_name .
            ($this->suffix ? ' ' . $this->suffix : '')
        );
    }

    public function scopeSeniors($query)
    {
        return $query->where('age', '>=', 60);
    }

    public function scopeChildren($query)
    {
        return $query->where('age', '<=', 17);
    }

    public function scopeAdults($query)
    {
        return $query->whereBetween('age', [18, 59]);
    }
}