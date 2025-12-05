<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ForceChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // при необходимости можно добавить Gate/Policy
    }

    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                Password::min(8)->letters()->numbers(), // минимум 8 символов, буквы и цифры
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required'   => 'New password is required.',
            'password.min'        => 'New password must be at least :min characters.',
            'password.confirmed'  => 'Passwords do not match.',
        ];
    }
}
