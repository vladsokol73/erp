<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketFormFieldCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:64'],
            'label'            => ['required', 'string', 'max:64'],
            'type'             => [
                'required',
                'string',
                'in:text,number,select,multiselect,country,textarea,date,file,checkbox,project',
            ],

            'is_required'      => ['required', 'boolean'],
            'options'          => ['sometimes', 'array'],
            'options.*'        => ['string'],

            'validation_rules'            => ['sometimes', 'array'],
            'validation_rules.*.type'     => [
                'required',
                'string',
                'in:email,url,max_length,min_length,max_number,min_number,min_date,max_date,file_type,contains,not_contains',
            ],
            'validation_rules.*.value'    => ['nullable'],
        ];
    }
}
