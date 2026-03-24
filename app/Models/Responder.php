<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'responder_id',
        'contact_number',
        'assigned_area',
        'address',
    ];
}