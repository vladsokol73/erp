<?php

namespace App\DTO\Ticket;
use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketFieldValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketFieldValuesListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $value,
        #[LiteralTypeScriptType('TicketFormFieldDto')]
        public readonly TicketFormFieldDto $formField,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'ticketFormFieldDto' => $this->formField,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketFieldValue)) {
            throw new \InvalidArgumentException('Expected TicketFieldValue instance');
        }

        return new self(
            id: $model->id,
            value: $model->value,
            formField: TicketFormFieldDto::fromModel($model->field()->withTrashed()->first()),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
