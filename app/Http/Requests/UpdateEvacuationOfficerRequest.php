<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvacuationOfficerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'assigned_area' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ];
    }
}
