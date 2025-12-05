<?php

namespace App\Services\User;

use App\DTO\User\PermissionDto;
use App\Models\User\Permission;
use App\Models\User\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

/**
 * Сервис для управления разрешениями пользователей.
 */
class PermissionService
{
    /**
     * Получить список всех разрешений в системе.
     *
     * @return PermissionDto[]
     */
    public function getPermissions(): array
    {
        return PermissionDto::fromCollection(
            Permission::query()->get()
        );
    }

    /**
     * Получить список разрешений пользователя.
     *
     * @param ?\App\Models\User\User $user
     * @return PermissionDto[]
     */
    public function getUserPermissions(?User $user = null): array
    {
        $user = $user ?? Auth::user();

        return PermissionDto::fromCollection(
            $user->permissions
        );
    }

    /**
     * Проверка, имеет ли пользователь указанное разрешение.
     *
     * @param string $permission Название разрешения (guard_name)
     * @param mixed|null $resource Связанный ресурс (резерв)
     * @param User|null $user Пользователь (по умолчанию — текущий)
     * @return bool
     */
    public function hasPermission(string $permission, mixed $resource = null, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user?->hasPermissionTo($permission) ?? false;
    }

    /**
     * Проверка, имеет ли пользователь хотя бы одно из указанных разрешений.
     *
     * @param string[] $permissions
     * @param \App\Models\User\User|null $user
     * @return bool
     */
    public function hasAnyPermission(array $permissions, ?User $user = null): bool
    {
        $user = $this->prepareUser($user);
        if (!$user) return false;

        $allPermissions = array_unique(array_merge(
            $user->permissions->pluck('guard_name')->toArray(),
            $user->roles->flatMap->permissions->pluck('guard_name')->toArray()
        ));

        return count(array_intersect($permissions, $allPermissions)) > 0;
    }

    /**
     * Проверка, имеет ли пользователь все указанные разрешения.
     *
     * @param string[] $permissions
     * @param User|null $user
     * @return bool
     */
    public function hasAllPermissions(array $permissions, ?User $user = null): bool
    {
        $user = $this->prepareUser($user);
        if (!$user) return false;

        $allPermissions = array_unique(array_merge(
            $user->permissions->pluck('guard_name')->toArray(),
            $user->roles->flatMap->permissions->pluck('guard_name')->toArray()
        ));

        return empty(array_diff($permissions, $allPermissions));
    }

    /**
     * Проверка доступа с выбросом исключения при отказе.
     *
     * @param string $permission
     * @param mixed|null $resource
     * @param User|null $user
     * @throws AuthorizationException
     */
    public function authorize(string $permission, mixed $resource = null, ?User $user = null): void
    {
        if (!$this->hasPermission($permission, $resource, $user)) {
            throw new AuthorizationException('Access denied');
        }
    }

    /**
     * Назначить пользователю одно или несколько разрешений.
     *
     * @param User $user
     * @param string|array ...$permissions
     * @return void
     */
    public function give(User $user, string|array ...$permissions): void
    {
        $user->givePermissionsTo(...$permissions);
    }

    /**
     * Удалить одно или несколько разрешений у пользователя.
     *
     * @param User $user
     * @param string|array ...$permissions
     * @return void
     */
    public function revoke(User $user, string|array ...$permissions): void
    {
        $user->deletePermissions(...$permissions);
    }

    /**
     * Обновить разрешения пользователя — удалить все и назначить новые.
     *
     * @param \App\Models\User\User $user
     * @param string|array ...$permissions
     * @return void
     */
    public function refresh(User $user, string|array ...$permissions): void
    {
        $user->refreshPermissions(...$permissions);
    }

    /**
     * Синхронизировать разрешения пользователя — оставить только указанные.
     *
     * @param \App\Models\User\User $user
     * @param string[] $permissions
     * @return void
     */
    public function sync(User $user, array $permissions): void
    {
        $resolved = Permission::whereIn('guard_name', $permissions)->get();
        $user->permissions()->sync($resolved);
    }

    /**
     * Убедиться, что связи permissions и roles.permissions загружены.
     *
     * @param User|null $user
     * @return User|null
     */
    private function prepareUser(?User $user): ?User
    {
        $user = $user ?? Auth::user();
        if (!$user) return null;

        $user->loadMissing(['permissions', 'roles.permissions']);
        return $user;
    }
}
