<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required | string | max:255',
            'style' => 'required | string | max:64',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.string' => 'Tag name must be a string.',
            'name.max' => 'Tag name cannot be longer than 255 characters.',
            'style.required' => 'Tag style is required.',
            'style.string' => 'Tag style must be a string.',
            'style.max' => 'Tag style cannot be longer than 64 characters.',
        ];
    }
}
