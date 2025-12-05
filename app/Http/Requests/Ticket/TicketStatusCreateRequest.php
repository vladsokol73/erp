<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketStatusCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'color'      => ['required', 'string'],
            //'regex:/^#?[0-9A-Fa-f]{6}$/'
            'is_default' => ['sometimes', 'boolean'],
            'is_final'   => ['sometimes', 'boolean'],
            'is_approval' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
