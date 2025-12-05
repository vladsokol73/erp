<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:favorite,unfavorite'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Favorite action type is required.',
            'type.string' => 'Favorite action type must be a string.',
            'type.in' => 'Favorite action type must be either "favorite" or "unfavorite".',
        ];
    }
}
