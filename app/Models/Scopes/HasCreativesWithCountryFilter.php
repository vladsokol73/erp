<?php

namespace App\Models\Scopes;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;

trait HasCreativesWithCountryFilter
{
    public function scopeHasCreativesInAvailableCountries(Builder $query, ?User $user): Builder
    {
        $available = $user->available_countries ?? null;

        if ($available === null) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('creatives', function ($q) use ($available) {
            if (!in_array('all', $available, true)) {
                $q->whereIn('country_id', $available);
            }
        });
    }
}
