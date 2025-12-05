<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Enums\ValidationRuleType;
use App\Models\Ticket\TicketFormField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketFormFieldListDto implements FromModelInterface, FromCollectionInterface
{
    /**
     * @param ValidationRuleDto[] $validation_rules
     * @param string[]            $options
     */
    public function __construct(
        public readonly int                  $id,
        public readonly string               $name,
        public readonly string               $label,
        public readonly string               $type,
        #[LiteralTypeScriptType('ValidationRuleDto[]')]
        public readonly array                $validation_rules,
        public readonly array                $options,
        public readonly bool                 $is_required,
        public readonly ?string              $created_at,
    ) {}

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'label'            => $this->label,
            'type'             => $this->type,
            'validation_rules' => array_map(
                fn(ValidationRuleDto $rule) => [
                    'type'  => $rule->type->value,
                    'value' => $rule->value,
                ],
                $this->validation_rules
            ),
            'options'          => $this->options,
            'is_required'      => $this->is_required,
            'created_at'       => $this->created_at,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (! $model instanceof TicketFormField) {
            throw new \InvalidArgumentException('Expected TicketFormField instance');
        }

        // Преобразуем сырые данные из БД в ValidationRuleDto[]
        $rules = array_map(
            fn(array $r) => new ValidationRuleDto(
                ValidationRuleType::from($r['type']),
                $r['value'] ?? null,
            ),
            $model->validation_rules ?? []
        );

        return new self(
            id:                $model->id,
            name:              $model->name,
            label:             $model->label,
            type:              $model->type,
            validation_rules:  $rules,
            options:           $model->options ?? [],
            is_required:       $model->is_required,
            created_at:        optional($model->created_at)?->toDateTimeString(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection
            ->map(fn($item) => static::fromModel($item))
            ->toArray();
    }
}
