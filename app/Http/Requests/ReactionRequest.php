<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:like,dislike'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Reaction type is required.',
            'type.string' => 'Reaction type must be a string.',
            'type.in' => 'Reaction type must be either "like" or "dislike".',
        ];
    }
}
