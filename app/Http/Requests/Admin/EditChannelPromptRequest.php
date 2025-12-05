<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RoleEnum;

class EditChannelPromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'prompt.required' => 'Prompt is required.',
            'prompt.string' => 'Prompt must be a string.',
            'prompt.max' => 'Prompt cannot exceed 10000 characters.',
        ];
    }
}
