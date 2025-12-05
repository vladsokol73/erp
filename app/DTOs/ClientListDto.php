<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\clients\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ClientListDto extends BaseDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $tg_id,
        public readonly ?string $clickid,
        public readonly ?string $c2d_channel_id,
        public readonly ?string $created_at,
        public readonly ?string $updated_at
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Client)) {
            throw new \InvalidArgumentException('Ожидалась модель типа Client');
        }

        return new self(
            id: $model->id,
            tg_id: $model->tg_id,
            clickid: $model->clickid,
            c2d_channel_id: $model->c2d_channel_id,
            created_at: $model->created_at ? $model->created_at->format('Y-m-d H:i:s') : null,
            updated_at: $model->updated_at ? $model->updated_at->format('Y-m-d H:i:s') : null
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($client) => static::fromModel($client))->toArray();
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tg_id' => $this->tg_id,
            'clickid' => $this->clickid,
            'c2d_channel_id' => $this->c2d_channel_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
