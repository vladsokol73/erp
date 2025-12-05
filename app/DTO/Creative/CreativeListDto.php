<?php

namespace App\DTO\Creative;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelWithUserInterface;
use App\Contracts\DTOs\ToArrayInterface;
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
class CreativeListDto implements FromModelWithUserInterface, FromCollectionInterface, ToArrayInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $url,
        public readonly string $type,
        public readonly CountryDto $country,
        public readonly int $likes_count,
        public readonly int $dislikes_count,
        public readonly array $comments,
        public readonly string|null $resolution,
        public readonly int $user_id,
        public readonly string $created_at,
        #[LiteralTypeScriptType('CreativeStatisticDto')]
        public readonly CreativeStatisticDto|null $statistic,
        #[LiteralTypeScriptType('TagDto[]')]
        public readonly array|null $tags = [],
        public readonly string|null $thumbnail = null,
        public readonly bool $user_liked = false,
        public readonly bool $user_disliked = false,
        public readonly bool $favorite = false,

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
            'likes_count' => $this->likes_count,
            'dislikes_count' => $this->dislikes_count,
            'user_liked' => $this->user_liked,
            'user_disliked' => $this->user_disliked,
            'resolution' => $this->resolution,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'statistic' => $this->statistic,
            'tags' => $this->tags,
            'thumbnail' => $this->thumbnail,
            'favorite' => $this->favorite
        ];
    }

    public static function fromModelWithUser(Model $model, User|null $user = null): static
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $thumbnailService = app(ThumbnailService::class);
        $likeService = app(LikeService::class);
        $favoriteService = app(FavoriteService::class);
        $commentService = app(CommentService::class);
        $tagService = app(TagService::class);


        $thumbnail = $model->type === 'video'
            ? $thumbnailService->getUrl($model->code)
            : null;

        $likesStats = $likeService->getStats($model);

        $userInteraction = $likeService->getUserInteraction($model);

        $isFavorite = $favoriteService->isFavorite($model);

        $comments = Guard::resolve([
            AccessRule::permission(
                PermissionEnum::CREATIVES_COMMENTS->value,
                fn() => $commentService->getComments($model)
            ),
        ],
            $user,
            default: fn() => $commentService->getUserComments($model, $user)
        );

        return new self(
            id: $model->id,
            code: $model->code,
            url: $model->url,
            type: $model->type,
            country: CountryDto::fromModel($model->country),
            likes_count: $likesStats['likes'],
            dislikes_count: $likesStats['dislikes'],
            comments: $comments,
            resolution: $model->resolution ?? '',
            user_id: $model->user_id,
            created_at: $model->created_at?->format('Y-m-d H:i:s'),
            statistic: $model->creativeStatistic()
                ? CreativeStatisticDto::fromModel($model->creativeStatistic())
                : new CreativeStatisticDto(
                    code: $model->code,
                    clicks: 0,
                    ctr: 0,
                    leads: 0,
                    date: now()->format('Y-m-d')
                ),
            tags: TagDto::fromCollection($tagService->getTagsByCreative($model)),
            thumbnail: $thumbnail,
            user_liked: $userInteraction['liked'],
            user_disliked: $userInteraction['disliked'],
            favorite: $isFavorite
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModelWithUser($item))->toArray();
    }
}
