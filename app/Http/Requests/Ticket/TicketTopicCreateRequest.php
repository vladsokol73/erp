<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketTopicCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:64'],
            'is_active'   => ['required', 'boolean'],

            // approval — одиночный объект
            'approval'                        => ['required', 'array'],

            'approval.*.responsible_model_name'=> ['required_with:responsible', 'string', 'in:User,Role,Permission'],
            'approval.*.responsible_id'        => ['required_with:responsible', 'integer', 'min:1'],
            'approval.*.responsible_title'     => ['nullable', 'string', 'max:255'],

            // responsible — массив объектов
            'responsible'                         => ['nullable', 'array'],
            'responsible.*.responsible_model_name'=> ['required_with:responsible', 'string', 'in:User,Role,Permission'],
            'responsible.*.responsible_id'        => ['required_with:responsible', 'integer', 'min:1'],
            'responsible.*.responsible_title'     => ['nullable', 'string', 'max:255'],

            'fields' => ['nullable', 'array'],
            'fields.*.id'   => ['required_with:fields', 'integer', 'exists:ticket_form_fields,id'],
            'fields.*.name'       => ['required_with:fields', 'string', 'max:255'],
            'fields.*.label'      => ['required_with:fields', 'string', 'max:255'],
            'fields.*.type'       => ['required_with:fields', 'string'],
            'fields.*.is_required'=> ['required_with:fields', 'boolean'],
            'fields.*.options'    => ['nullable', 'array'],
            'fields.*.options.*'  => ['string'],
            'fields.*.validation_rules'             => ['nullable', 'array'],
            'fields.*.validation_rules.*.type'      => [
                'required',
                'string',
                'in:email,url,max_length,min_length,max_number,min_number,min_date,max_date,file_type,contains,not_contains',
            ],
            'fields.*.validation_rules.*.value'     => ['nullable'],

        ];
    }
}
