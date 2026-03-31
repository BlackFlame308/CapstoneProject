<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'household_id' => 'required|exists:households,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'sex' => 'required|in:male,female,other',
            'civil_status' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'education_level' => 'nullable|string|max:100',
            'is_graduate' => 'required|boolean',
            'is_pwd' => 'required|boolean',
        ];
    }
}
