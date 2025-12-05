<?php

namespace App\Services\Creative;

use App\DTO\Creative\TagCreateDto;
use App\DTO\Creative\TagDto;
use App\DTO\Creative\TagListDto;
use App\DTO\PaginatedListDto;
use App\Models\Creative;
use App\Models\Tag;
use App\Models\User\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TagService
{
    public function getTag(int $tagId): Tag
    {
        return Tag::query()->findOrFail($tagId);
    }

    public function getTagPaginated(int $page, string $search, int $perPage = 10): PaginatedListDto
    {
        $query = Tag::query()
            ->orderByDesc('created_at')
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn($tag) => TagListDto::fromModel($tag)
        );
    }

    public function getTags(): Collection
    {
        return Tag::query()->get();
    }

    public function getTagsByCreative(Creative $creative): Collection
    {
        return $creative->tags()->orderBy('created_at')->get();
    }

    public function getTagsWithCreatives(?User $user = null): array
    {
        $user ??= Auth::user();

        return TagDto::fromCollection(
            Tag::query()
                ->hasCreativesInAvailableCountries($user)
                ->hasCreativesWithAvailableTags($user)
                ->get()
        );
    }

    public function createTag(TagCreateDto $tagCreateDto): Tag
    {
        $tag = new Tag([
            'name' => $tagCreateDto->name,
            'style' => $tagCreateDto->style
        ]);

        $tag->save();

        return $tag;
    }

    public function updateTag(int $tagId, TagCreateDto $dto): Tag
    {
        $tag = $this->getTag($tagId);

        $tag->update([
            'name' => $dto->name,
            'style' => $dto->style,
        ]);

        return $tag;
    }

    public function deleteTag(int $tagId): void
    {
        $tag = $this->getTag($tagId);
        $tag->delete();
    }
}
