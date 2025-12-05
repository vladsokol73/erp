<?php

namespace App\Http\Requests\Creative;

use Illuminate\Foundation\Http\FormRequest;

class CreativeFileDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'URL обязателен для удаления файла.',
        ];
    }
}
