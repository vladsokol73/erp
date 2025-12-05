<?php

namespace App\DTO;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Operator\Operator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OperatorListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly int $operator_id,
        public readonly ?string $name,
        public readonly bool $has_ai_retention,
        public readonly string $created_at,
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Operator)) {
            throw new \InvalidArgumentException('Expected Operator type model');
        }

        return new self(
            id: $model->id,
            operator_id: $model->operator_id,
            name: $model->name,
            has_ai_retention: $model->hasFlag('ai_retention'),
            created_at: $model->created_at->toISOString(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection
            ->map(fn($item) => static::fromModel($item))
            ->toArray();
    }
}
