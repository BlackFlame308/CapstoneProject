<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisasterEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'disaster_type',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}