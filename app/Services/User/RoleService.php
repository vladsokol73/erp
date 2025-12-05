<?php

namespace App\Services\User;

use App\DTO\User\RoleDto;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

/**
 * Сервис для управления ролями пользователей.
 */
class RoleService
{
    /**
     * Получить список всех ролей в системе.
     *
     * @return RoleDto[]
     */
    public function getRoles(): array
    {
        return RoleDto::fromCollection(Role::query()->get());
    }

    /**
     * Получить список ролей пользователя.
     *
     * @param ?User $user
     * @return RoleDto[]
     */
    public function getUserRoles(?User $user = null): array
    {
        $user = $user ?? Auth::user();

        return RoleDto::fromCollection(
            $user->roles
        );
    }

    /**
     * Проверка, имеет ли пользователь указанную роль.
     *
     * @param string $role Название роли
     * @param User|null $user Пользователь (по умолчанию текущий авторизованный)
     * @return bool
     */
    public function hasRole(string $role, ?User $user = null): bool
    {
        $user = $this->prepareUser($user);
        return $user?->roles->contains('title', $role) ?? false;
    }

    /**
     * Проверка, имеет ли пользователь хотя бы одну из указанных ролей.
     *
     * @param string[] $roles
     * @param User|null $user
     * @return bool
     */
    public function hasAnyRole(array $roles, ?User $user = null): bool
    {
        $user = $this->prepareUser($user);
        return $user && $user->roles->pluck('title')->intersect($roles)->isNotEmpty();
    }

    /**
     * Проверка, имеет ли пользователь все указанные роли.
     *
     * @param string[] $roles
     * @param \App\Models\User\User|null $user
     * @return bool
     */
    public function hasAllRoles(array $roles, ?User $user = null): bool
    {
        $user = $this->prepareUser($user);
        return $user && empty(array_diff($roles, $user->roles->pluck('title')->toArray()));
    }

    /**
     * Назначить пользователю одну или несколько ролей.
     *
     * @param User $user
     * @param string|array ...$roles
     * @return void
     */
    public function give(User $user, string|array ...$roles): void
    {
        $user->giveRolesTo(...$roles);
    }

    /**
     * Удалить одну или несколько ролей у пользователя.
     *
     * @param User $user
     * @param string|array ...$roles
     * @return void
     */
    public function revoke(User $user, string|array ...$roles): void
    {
        $user->deleteRoles(...$roles);
    }

    /**
     * Обновить роли пользователя: удалить все старые и назначить новые.
     *
     * @param \App\Models\User\User $user
     * @param string|array ...$roles
     * @return void
     */
    public function refresh(User $user, string|array ...$roles): void
    {
        $user->refreshRoles(...$roles);
    }

    /**
     * Синхронизировать роли пользователя — оставить только указанные.
     *
     * @param User $user
     * @param string[] $roles
     * @return void
     */
    public function sync(User $user, array $roles): void
    {
        $resolved = Role::whereIn('title', $roles)->get();
        $user->roles()->sync($resolved);
    }

    /**
     * Убедиться, что у пользователя загружены связи roles.
     *
     * @param \App\Models\User\User|null $user
     * @return \App\Models\User\User|null
     */
    private function prepareUser(?User $user): ?User
    {
        $user = $user ?? Auth::user();
        if (!$user) return null;

        $user->loadMissing('roles');
        return $user;
    }
}
