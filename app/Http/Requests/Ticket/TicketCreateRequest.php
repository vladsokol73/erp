<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:ticket_categories,id',
            'topic_id'    => 'required|integer|exists:ticket_topics,id',
            'priority'    => 'required|string|in:low,middle,high',
            'fields'      => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'The "Category" field is required.',
            'category_id.integer'  => 'The "Category" must be an integer.',
            'category_id.exists'   => 'The selected category does not exist.',

            'topic_id.required' => 'The "Topic" field is required.',
            'topic_id.integer'  => 'The "Topic" must be an integer.',
            'topic_id.exists'   => 'The selected topic does not exist.',

            'priority.required' => 'The "Priority" field is required.',
            'priority.string'   => 'The "Priority" field must be a string.',
            'priority.in'       => 'The "Priority" must be one of: low, middle, high.',

            'fields.array'      => 'The "Fields" must be an array.',
        ];
    }
}
