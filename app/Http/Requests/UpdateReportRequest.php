<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:Evacuation Capacity,Disaster Event Summary,Sitio-Based Impact',
            'disaster_event_id' => 'nullable|exists:disaster_events,id',
            'content' => 'required|array',
        ];
    }
}
