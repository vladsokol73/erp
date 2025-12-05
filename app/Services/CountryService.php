<?php

namespace App\Services;

use App\DTO\CountryDto;
use App\Models\Country;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

class CountryService
{
    public function getCountries(): array
    {
        return CountryDto::fromCollection(
            Country::query()->get()
        );
    }

    public function getCountriesWithCreatives(?User $user = null): array
    {
        $user ??= Auth::user();

        return CountryDto::fromCollection(
            Country::query()
                ->hasCreativesInAvailableCountries($user)
                ->hasCreativesWithAvailableTags($user)
                ->get()
        );
    }



}
