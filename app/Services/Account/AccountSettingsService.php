<?php

namespace App\Services\Account;

use App\Models\Country;
use App\Models\Operators\Channel as OperatorsChannel;
use App\Models\Operators\Operator as OperatorsOperator;


use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountSettingsService
{
    /**
     * Смена пароля с проверкой текущего.
     *
     * @throws ValidationException
     */
    public function changePassword(string $currentPassword, string $newPassword, ?User $user = null): void
    {
        /** @var User|null $user */
        $user = $user ?? Auth::user();

        if (!$user) {
            throw ValidationException::withMessages([
                'current_password' => ['Unauthenticated.'],
            ]);
        }

        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($newPassword),
        ])->save();
    }

    /**
     * Смена пароля без проверки текущего.
     * Использовать только для сценариев "принудительная смена".
     *
     * @throws ValidationException
     */
    public function forceChangePassword(string $newPassword, ?User $user = null): void
    {
        /** @var User|null $user */
        $user = $user ?? Auth::user();

        if (!$user) {
            throw ValidationException::withMessages([
                'password' => ['Unauthenticated.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($newPassword),
        ])->save();

        $user->removeFlag('must_change_password');
    }

    public function resolveAvailableCountries(array|string|null $availableCountries): array|string|null
    {
        if (is_null($availableCountries) || (is_array($availableCountries) && in_array('all', $availableCountries, true))) {
            return $availableCountries;
        }

        $countryIds = is_array($availableCountries) ? array_filter($availableCountries) : $availableCountries;
        if (empty($countryIds)) {
            return [];
        }

        return Country::whereIn('id', (array) $countryIds)
            ->pluck('name')
            ->toArray();
    }

    public function resolveAvailableChannels(array|string|null $availableChannels): array|string|null
    {
        if (is_null($availableChannels) || (is_array($availableChannels) && in_array('all', $availableChannels, true))) {
            return $availableChannels;
        }

        $clientIds = is_array($availableChannels) ? array_filter($availableChannels) : $availableChannels;
        if (empty($clientIds)) {
            return [];
        }

        return OperatorsChannel::whereIn('channel_id', (array) $clientIds)
            ->get()
            ->map(fn($channel) => $channel->name ?? $channel->id)
            ->toArray();
    }

    public function resolveAvailableOperators(array|string|null $availableOperators): array|string|null
    {
        if (is_null($availableOperators) || (is_array($availableOperators) && in_array('all', $availableOperators, true))) {
            return $availableOperators;
        }

        $operatorIds = is_array($availableOperators) ? array_filter($availableOperators) : $availableOperators;
        if (empty($operatorIds)) {
            return [];
        }

        return OperatorsOperator::whereIn('operator_id', (array) $operatorIds)
            ->get()
            ->map(fn($operator) => $operator->name ?? $operator->id)
            ->toArray();
    }
}



