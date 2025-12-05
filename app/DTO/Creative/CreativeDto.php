<?php

namespace App\DTO\Creative;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Contracts\DTOs\FromModelWithUserInterface;
use App\DTO\CountryDto;
use App\Enums\PermissionEnum;
use App\Facades\Guard;
use App\Models\Creative;
use App\Models\User\User;
use App\Services\CommentService;
use App\Services\Creative\TagService;
use App\Services\FavoriteService;
use App\Services\LikeService;
use App\Services\ThumbnailService;
use App\Support\AccessRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CreativeDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $url,
        public readonly string $type,
        public readonly CountryDto $country,
        public readonly int $user_id,
        public readonly string $created_at,
        public readonly string|null $thumbnail = null,

    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'url' => $this->url,
            'type' => $this->type,
            'country' => $this->country,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'thumbnail' => $this->thumbnail,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $thumbnailService = app(ThumbnailService::class);

        $thumbnail = $model->type === 'video'
            ? $thumbnailService->getUrl($model->code)
            : null;

        return new self(
            id: $model->id,
            code: $model->code,
            url: $model->url,
            type: $model->type,
            country: CountryDto::fromModel($model->country),
            user_id: $model->user_id,
            created_at: $model->created_at?->format('Y-m-d H:i:s'),
            thumbnail: $thumbnail,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
