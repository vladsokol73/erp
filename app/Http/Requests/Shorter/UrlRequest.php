<?php

namespace App\Http\Requests\Shorter;

use Illuminate\Foundation\Http\FormRequest;

class UrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Если нужна авторизация — можно добавить логику
        return true;
    }

    public function rules(): array
    {
        return [
            'original_url' => [
                'required',
                'string',
                'url',
                'max:2083', // безопасный лимит
            ],

            'short_code' => [
                'nullable',
                'string',
                'size:6',
                'regex:/^[a-zA-Z0-9\-_]+$/', // безопасный набор символов
            ],

            'domain' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'Original URL is required.',
            'original_url.url' => 'Original URL is not valid.',
            'original_url.max' => 'Original URL is too long.',

            'domain.required' => 'Domain is required.',
            'domain.max' => 'Domain is too long.',

            'short_code.size' => 'Short code must be exactly 6 characters.',
            'short_code.regex' => 'Short code may contain only letters, numbers, dashes and underscores.',
        ];
    }
}
