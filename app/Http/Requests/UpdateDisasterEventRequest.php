<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDisasterEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'disaster_type' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ];
    }
}
