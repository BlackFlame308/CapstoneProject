<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHouseholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $householdId = $this->route('household')->id ?? null;

        return [
            'address' => 'required|string|max:255',
            'sitio' => 'nullable|string|max:255',
            'purok' => 'required|string|max:255',
            'emergency_contact' => 'required|string|max:255',
            'members' => 'nullable|array',
            'members.*.first_name' => 'required_with:members|string|max:255',
            'members.*.last_name' => 'required_with:members|string|max:255',
            'members.*.middle_name' => 'nullable|string|max:255',
            'members.*.suffix' => 'nullable|string|max:50',
            'members.*.birth_date' => 'required_with:members|date',
            'members.*.birth_place' => 'nullable|string|max:255',
            'members.*.sex' => 'required_with:members|in:male,female,other',
            'members.*.civil_status' => 'nullable|string|max:100',
            'members.*.religion' => 'nullable|string|max:100',
            'members.*.citizenship' => 'nullable|string|max:100',
            'members.*.profession' => 'nullable|string|max:100',
            'members.*.contact_number' => 'nullable|string|max:50',
            'members.*.email' => 'nullable|email|max:255',
            'members.*.education_level' => 'nullable|string|max:100',
            'members.*.is_graduate' => 'nullable|boolean',
            'members.*.is_pwd' => 'nullable|boolean',
        ];
    }
}
