<?php

namespace App\DTO;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Operator\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ChannelListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly int $channel_id,
        public readonly string|null $name,
        public readonly bool $has_ai_retention,
        public readonly string $created_at,
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Channel)) {
            throw new \InvalidArgumentException('Expected Channel type model');
        }

        return new self(
            id: $model->id,
            channel_id: $model->channel_id,
            name: $model->name,
            has_ai_retention: $model->hasFlag('ai_retention'),
            created_at: $model->created_at->toISOString(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
