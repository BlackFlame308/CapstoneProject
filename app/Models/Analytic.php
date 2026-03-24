<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sitio',
        'total_households',
        'total_population',
        'total_seniors',
        'total_pwd',
        'total_children',
        'total_adults',
    ];
}