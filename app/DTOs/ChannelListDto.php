<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Operator\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ChannelListDto extends BaseDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $channel_id,
        public readonly string $name
    ) {
    }

    public function toArray(): array
    {
        return [
            'channel_id' => $this->channel_id,
            'name' => $this->name
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Channel)) {
            throw new \InvalidArgumentException('Ожидалась модель типа Channel');
        }

        return new self(
            channel_id: $model->id,
            name: $model->name
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($channel) => static::fromModel($channel))->toArray();
    }
}
