<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GChatSsoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Доступен всем (гости и авторизованные), дальнейшая логика в контроллере
        return true;
    }

    public function rules(): array
    {
        return [
            'redirect_uri' => ['required', 'url'],
            'state' => ['nullable', 'string', 'max:512'],
        ];
    }
}


