<?php

namespace App\Models\Scopes;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;

trait HasCreativesWithTagFilter
{
    public function scopeHasCreativesWithAvailableTags(Builder $query, ?User $user): Builder
    {
        $available = $user->available_tags ?? null;

        // Если теги не заданы, то считаем, что креативы все скрыты
        if ($available === null) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('creatives', function (Builder $q) use ($available) {
            // Если доступны не все, то фильтруем
            if (!in_array('all', $available, true)) {
                $available = array_map('intval', $available);

                $q->whereDoesntHave('tags', function ($tagQuery) use ($available) {
                    $tagQuery->whereNotIn('tags.id', $available);
                });
            }
        });
    }
}
