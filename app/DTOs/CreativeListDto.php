<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Creative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CreativeListDto extends BaseDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $url,
        public readonly string $type,
        public readonly ?string $resolution = null,
        public readonly ?int $country_id = null,
        public readonly ?int $user_id = null,
        public readonly ?string $created_at = null,
        public readonly ?array $tags = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'url' => $this->url,
            'type' => $this->type,
            'resolution' => $this->resolution,
            'country_id' => $this->country_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'tags' => $this->tags
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Ожидалась модель типа Creative');
        }

        return new self(
            id: $model->id,
            code: $model->code,
            url: $model->url,
            type: $model->type,
            resolution: $model->resolution,
            country_id: $model->country_id,
            user_id: $model->user_id,
            created_at: $model->created_at?->format('Y-m-d H:i:s'),
            tags: $model->tags->map(fn($tag) => ['id' => $tag->id, 'name' => $tag->name])->toArray()
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($creative) => static::fromModel($creative))->toArray();
    }
}
