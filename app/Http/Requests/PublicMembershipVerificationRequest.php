<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicMembershipVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['membership_number' => strtoupper(trim((string) $this->membership_number))]);
    }

    public function rules(): array
    {
        return ['membership_number' => ['required', 'string', 'max:50', 'regex:/^ISHEP-\d{4}-\d{6}$/']];
    }
}
