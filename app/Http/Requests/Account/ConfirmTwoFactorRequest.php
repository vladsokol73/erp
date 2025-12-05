<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'numeric', 'digits:6'],
            'secret' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The confirmation code is required.',
            'code.numeric'  => 'The confirmation code must be numeric.',
            'code.digits'   => 'The confirmation code must be exactly 6 digits.',
            'secret.required' => 'The secret key is required.',
        ];
    }
}
