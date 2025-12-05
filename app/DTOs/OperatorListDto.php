<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Operator\Operator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OperatorListDto extends BaseDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $operator_id,
        public readonly string $name
    ) {
    }

    public function toArray(): array
    {
        return [
            'operator_id' => $this->operator_id,
            'name' => $this->name
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Operator)) {
            throw new \InvalidArgumentException('Ожидалась модель типа Operator');
        }

        return new self(
            operator_id: $model->operator_id,
            name: $model->name
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($operator) => static::fromModel($operator))->toArray();
    }
}
