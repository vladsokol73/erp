<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketFormField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketFormFieldDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        public readonly array $validation_rules,
        public readonly array $options,
        public readonly bool $is_required,
    ) {}

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'label'            => $this->label,
            'type'             => $this->type,
            'validation_rules' => $this->validation_rules,
            'options'          => $this->options,
            'is_required'      => $this->is_required,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!$model instanceof TicketFormField) {
            throw new \InvalidArgumentException('Expected TicketFormField instance');
        }

        return new self(
            id: $model->id,
            name: $model->name,
            label: $model->label,
            type: $model->type,
            validation_rules: $model->validation_rules,
            options: $model->options,
            is_required: $model->is_required,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            label: $data['label'],
            type: $data['type'],
            validation_rules: $data['validation_rules'] ?? [],
            options: $data['options'] ?? [],
            is_required: $data['is_required'],
        );
    }

}
