<?php

namespace App\Services\Account;

use App\Models\User\User;

final class PasswordPolicyService
{
    /**
     * Определяет, нужно ли пользователю принудительно сменить пароль.
     *
     * @param User|null $user Пользователь или null
     * @return bool true, если установлено требование смены пароля; иначе false
     */
    public function mustForceChange(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasFlag('must_change_password');
    }
}
