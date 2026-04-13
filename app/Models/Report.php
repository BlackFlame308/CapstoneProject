<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_module',
        'report_type',
        'content_data',
        'date_timestamp',
    ];

    protected $casts = [
        'content_data' => 'array',
        'date_timestamp' => 'datetime',
    ];
}