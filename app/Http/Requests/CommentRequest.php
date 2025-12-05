<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Comment is required.',
            'comment.string' => 'Comment must be a string.',
            'comment.min' => 'Comment must not be empty.',
        ];
    }
}
