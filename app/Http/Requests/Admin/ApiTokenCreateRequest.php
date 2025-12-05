<?php

namespace App\Http\Requests\Admin;

use App\Enums\ApiServiceEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ApiTokenCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service' => ['required', 'max:64', new Enum(ApiServiceEnum::class)],
            'email'   => ['required', 'email', 'max:255'],
            'token'   => ['required', 'string', 'max:255'],
        ];
    }
}
