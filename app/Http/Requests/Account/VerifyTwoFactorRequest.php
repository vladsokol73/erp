<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The confirmation code is required.',
            'code.string'   => 'The confirmation code must be a string.',
            'code.regex'    => 'The confirmation code must be exactly 6 digits.',
        ];
    }
}
