<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class PlayerTicketCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_id'  => ['required', 'integer', 'exists:product_logs,player_id'],
            'type'       => ['required', 'string', 'in:fd,rd'],
            'tg_id'      => ['required', 'integer'],
            'screen'     => ['required', 'file'],
            'sum'        => ['required', 'numeric'],
        ];
    }
}
