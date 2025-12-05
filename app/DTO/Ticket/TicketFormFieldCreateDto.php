<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketFormFieldCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        public readonly array $validation_rules,
        public readonly array $options,
        public readonly bool $is_required,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            label: $request->input('label'),
            type: $request->input('type'),
            validation_rules: $request->input('validation_rules'),
            options: $request->input('options'),
            is_required: $request->input('is_required'),
        );
    }

    public function toArray(): array
    {
        return [
            'name'             => $this->name,
            'label'            => $this->label,
            'type'             => $this->type,
            'validation_rules' => $this->validation_rules,
            'options'          => $this->options,
            'is_required'      => $this->is_required,
        ];
    }
}
