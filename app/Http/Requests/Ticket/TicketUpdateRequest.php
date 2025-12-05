<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => 'nullable|integer',
            'priority'  => 'nullable|string|in:low,middle,high',
            'fields'    => 'nullable|array',
            'result'    => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'status_id.integer' => 'Status ID must be an integer.',

            'priority.string'   => 'Priority must be a string.',
            'priority.in'       => 'The "Priority" must be one of: low, middle, high.',

            'fields.array'    => 'Fields must be array.',
        ];
    }
}
