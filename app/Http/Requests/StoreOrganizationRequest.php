<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('application')) ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'registration_number' => ['nullable', 'string', 'max:100'], 'contact_email' => ['required', 'email'], 'contact_phone' => ['required', 'string', 'max:30'], 'province_id' => ['nullable', 'exists:provinces,id'], 'physical_address' => ['nullable', 'string'], 'website' => ['nullable', 'url', 'max:255']];
    }
}
