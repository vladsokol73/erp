<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketCategoryCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'is_active'   => ['required', 'boolean'],
            'statuses'    => ['nullable', 'array'],
            'statuses.*.id'         => ['required', 'integer', 'exists:ticket_statuses,id'],
            'statuses.*.name'       => ['nullable', 'string'],
            'statuses.*.slug'       => ['nullable', 'string'],
            'statuses.*.color'      => ['nullable', 'string'],
            'statuses.*.is_default' => ['nullable', 'boolean'],
            'statuses.*.is_final'   => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:64'],
        ];
    }
}
