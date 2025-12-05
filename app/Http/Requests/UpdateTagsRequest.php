<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tags' => ['required', 'array'],
            'tags.*' => ['string'], // если нужно, чтобы каждый тег был строкой
        ];
    }

    public function messages(): array
    {
        return [
            'tags.required' => 'Tags are required.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.string' => 'Each tag must be a string.',
        ];
    }
}
