<?php

namespace App\Http\Requests;

use App\Enums\StudentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentEligibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('application')) ?? false;
    }

    public function rules(): array
    {
        return ['category' => ['required', Rule::enum(StudentCategory::class)], 'institution_name' => ['nullable', 'string', 'max:255'], 'grade' => ['nullable', 'string', 'max:50'], 'academic_year' => ['nullable', 'integer', 'between:2000,2100']];
    }
}
