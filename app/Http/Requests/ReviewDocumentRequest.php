<?php

namespace App\Http\Requests;

use App\Enums\DocumentReviewStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('memberships.review') ?? false;
    }

    public function rules(): array
    {
        return ['review_status' => ['required', Rule::enum(DocumentReviewStatus::class)], 'review_notes' => ['nullable', 'string', 'max:5000']];
    }
}
