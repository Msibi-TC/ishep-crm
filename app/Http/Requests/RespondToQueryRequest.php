<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespondToQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('query')?->application) ?? false;
    }

    public function rules(): array
    {
        return ['response' => ['required', 'string', 'max:5000']];
    }
}
