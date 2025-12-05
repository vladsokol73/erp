<?php

namespace App\DTO\Log;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\ProductLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductLogListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly int $player_id,
        public readonly string $status,
        public readonly int $c2d_channel_id,
        public readonly int $tg_id,
        public readonly int $prod_id,
        public readonly string $dep_sum,
        public readonly int $operator_id,
        public readonly string $created_at,
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof ProductLog)) {
            throw new \InvalidArgumentException('Expected ProductLog type model');
        }

        return new self(
            id: $model->id,
            player_id: (int) $model->player_id,
            status: (string) $model->status,
            c2d_channel_id: (int) $model->c2d_channel_id,
            tg_id: (int) $model->tg_id,
            prod_id: (int) $model->prod_id,
            dep_sum: (string) $model->dep_sum,
            operator_id: (int) $model->operator_id,
            created_at: $model->created_at?->toDateTimeString() ?? '',
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
