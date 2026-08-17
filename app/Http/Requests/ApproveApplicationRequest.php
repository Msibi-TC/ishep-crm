<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('memberships.review') ?? false;
    }

    public function rules(): array
    {
        return ['reason' => ['nullable', 'string', 'max:5000']];
    }
}
