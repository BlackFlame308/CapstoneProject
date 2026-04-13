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
        'is_pwd',
        'philips_card_no',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birth_date',
        'birth_place',
        'sex',
        'civil_status',
        'religion',
        'residence_address',
        'citizenship',
        'profession',
        'education_level',
        'is_graduate',
        'contact_number',
        'email',
        'date_accomplished',
        'name_signature',
        'attested_by',
        'left_thumbmark',
        'right_thumbmark',
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

    public function getIsSeniorAttribute()
    {
        return $this->age >= 60;
    }

    public function getIsChildAttribute()
    {
        return $this->age < 18;
    }

    public function getVulnerabilityTypeAttribute()
    {
        if ($this->is_pwd) {
            return 'pwd';
        }

        if ($this->isSenior) {
            return 'senior';
        }

        if ($this->isChild) {
            return 'child';
        }

        return 'adult';
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