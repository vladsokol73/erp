<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'required|string|max:32',
            'email'               => 'required|string|email|max:256',
            'password'            => 'nullable|string|min:6|max:64',
            'role_id'             => 'required|integer|exists:roles,id',
            'permissions'         => 'nullable|array',
            'available_countries' => 'nullable|array',
            'available_tags'      => 'nullable|array',
            'available_channels'  => 'nullable|array',
            'available_operators' => 'nullable|array',
            'api_token_ids'       => 'nullable|array',
            'api_token_ids.*'     => 'integer|exists:api_tokens,id',
        ];
    }
}

