<?php

namespace App\Http\Requests\Shorter;

use Illuminate\Foundation\Http\FormRequest;

class DomainEditRequest extends FormRequest
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
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'redirect_url.required' => 'Redirect URL is required.',
            'redirect_url.url' => 'Redirect URL is not valid.',
            'redirect_url.max' => 'Redirect URL is too long.',

            'is_active.required' => 'Is active field is required.',
        ];
    }
}
