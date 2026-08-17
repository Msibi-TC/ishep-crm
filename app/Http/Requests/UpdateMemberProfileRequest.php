<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['first_name' => ['required', 'string', 'max:255'], 'middle_names' => ['nullable', 'string', 'max:255'], 'last_name' => ['required', 'string', 'max:255'], 'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'], 'phone' => ['required', 'string', 'max:30'], 'alternate_phone' => ['nullable', 'string', 'max:30'], 'province_id' => ['nullable', 'exists:provinces,id'], 'profession_id' => ['nullable', 'exists:professions,id'], 'address_line_1' => ['nullable', 'string', 'max:255'], 'address_line_2' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:255'], 'postal_code' => ['nullable', 'string', 'max:20']];
    }
}
