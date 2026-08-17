<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('memberships.review') ?? false;
    }

    public function rules(): array
    {
        return ['message' => ['required', 'string', 'max:5000']];
    }
}
