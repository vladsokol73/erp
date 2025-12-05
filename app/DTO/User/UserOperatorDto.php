<?php

namespace App\DTO\User;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserOperatorDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly ?int $operator_id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof User)) {
            throw new \InvalidArgumentException('Expected User type model');
        }

        return new self(
            operator_id: $model->operator_id,
            name: $model->name,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
