<?php

namespace App\Http\Requests\Shorter;

use Illuminate\Foundation\Http\FormRequest;

class DomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Можно добавить логику авторизации, если требуется
        return true;
    }

    public function rules(): array
    {
        return [
            'redirect_url' => 'required|url|max:2083',
            'domain' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'redirect_url.required' => 'Redirect URL is required.',
            'redirect_url.url' => 'Redirect URL is not valid.',
            'redirect_url.max' => 'Redirect URL is too long.',

            'domain.required' => 'Domain is required.',
        ];
    }
}
