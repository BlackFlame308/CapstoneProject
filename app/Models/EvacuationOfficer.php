<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvacuationOfficer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'officer_id',
        'contact_number',
        'assigned_area',
        'address',
    ];

    public function scopeByArea($query, $area)
    {
        return $query->where('assigned_area', $area);
    }
}