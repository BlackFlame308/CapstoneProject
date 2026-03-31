<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'content',
        'disaster_event_id',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function disasterEvent()
    {
        return $this->belongsTo(DisasterEvent::class);
    }
}