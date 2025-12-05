<?php

namespace App\Services\Ticket;

use App\DTO\PaginatedListDto;
use App\DTO\Ticket\TicketFormFieldCreateDto;
use App\DTO\Ticket\TicketFormFieldDto;
use App\DTO\Ticket\TicketFormFieldListDto;
use App\Models\Ticket\TicketFormField;

class TicketFormFieldService
{
    public function ticketFormField(int $fieldId): TicketFormField
    {
        return TicketFormField::query()->findOrFail($fieldId);
    }

    public function getFormFieldsPaginated(int $page, string $search): PaginatedListDto
    {
        $query = TicketFormField::query()
            ->orderBy('created_at', 'desc')
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: 10, page: $page),
            fn($tag) => TicketFormFieldListDto::fromModel($tag)
        );
    }

    public function getFormFields(): array
    {
        return TicketFormFieldDto::fromCollection(TicketFormField::query()->get());
    }

    public function createFormField(TicketFormFieldCreateDto $dto): TicketFormField
    {
        return TicketFormField::create([
            'name'             => $dto->name,
            'label'            => $dto->label,
            'type'             => $dto->type,
            'validation_rules' => $dto->validation_rules,
            'options'          => $dto->options,
            'is_required'      => $dto->is_required,
        ]);
    }

    public function updateFormField(int $fieldId, TicketFormFieldCreateDto $dto): TicketFormField
    {
        $formField = $this->ticketFormField($fieldId);

        $formField->update([
            'name' => $dto->name,
            'label' => $dto->label,
            'type' => $dto->type,
            'validation_rules' => $dto->validation_rules,
            'options' => $dto->options,
            'is_required' => $dto->is_required,
        ]);

        return $formField;
    }

    public function deleteFormField(int $fieldId): void
    {
        $formField = $this->ticketFormField($fieldId);
        $formField->delete();
    }
}
