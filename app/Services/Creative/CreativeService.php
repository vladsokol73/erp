<?php

namespace App\Services\Creative;

use App\DTO\Creative\CreativeListDto;
use App\DTO\PaginatedListDto;
use App\Models\Creative;
use App\Models\Tag;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

class CreativeService
{
    public function getCreative(int $creativeId): Creative
    {
        return Creative::findOrFail($creativeId);
    }

    public function getCreativeByCode(string $creativeCode): Creative
    {
        return Creative::where('code', $creativeCode)->firstOrFail();
    }

    public function getCreativePaginated(int $page, string $search, string $sort, array $filters, int $perPage, bool|null $isFavorite = null, User|null $user = null): PaginatedListDto
    {
        $user = $user ?? Auth::user();

        $available = $user->available_countries ?? null;

        if ($available === null) {
            return PaginatedListDto::empty();
        }

        $query = Creative::query()
            ->sort($sort)
            ->search($search)
            ->filter($filters)
            ->forUserAvailableCountries($user)
            ->forUserAvailableTags($user)
            ->favorite($isFavorite, $user);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn($creative) => CreativeListDto::fromModelWithUser($creative, $user)
        );
    }

    public function updateCreativeTags(int $creativeId, array $tagIds): void
    {
        $creative = $this->getCreative($creativeId);

        // Убедимся, что все переданные ID существуют
        $validTagIds = Tag::whereIn('id', $tagIds)->pluck('id')->all();

        $creative->tags()->sync($validTagIds);
    }

    /**
     * Создание креатива с загрузкой файла
     * @param array $creativeCreateDTOs
     * @return array|Creative
     */
    public function createCreative(array $creativeCreateDTOs): array|Creative
    {
        $creatives = [];
        foreach ($creativeCreateDTOs as $dto) {
            $creative = new Creative([
                'code' => $dto->code,
                'url' => $dto->url,
                'type' => $dto->type,
                'resolution' => $dto->resolution,
                'country_id' => $dto->country_id,
                'user_id' => $dto->user_id,
            ]);
            $creative->save();

            $creative->tags()->sync($dto->tags);

            $creatives[] = $creative;
        }
        return $creatives;
    }

    public function deleteCreative(int $id): void
    {
        $creative = $this->getCreative($id);

        $creative->delete();
    }
}
