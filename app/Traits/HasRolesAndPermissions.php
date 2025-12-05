<?php

namespace App\Traits;

use App\Models\User\Permission;
use App\Models\User\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Трейт для моделей, имеющих роли и разрешения.
 *
 * Предназначен для использования в модели User.
 */
trait HasRolesAndPermissions
{
    /**
     * Роли, назначенные пользователю.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Разрешения, назначенные пользователю напрямую.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Проверка, имеет ли пользователь конкретную роль.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->roles->contains('title', $role);
    }

    /**
     * Проверка, имеет ли пользователь хотя бы одну из ролей.
     *
     * @param string[] $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('title')->intersect($roles)->isNotEmpty();
    }

    /**
     * Проверка, имеет ли пользователь все указанные роли.
     *
     * @param string[] $roles
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        return empty(array_diff($roles, $this->roles->pluck('title')->toArray()));
    }

    /**
     * Проверка, есть ли у пользователя прямое разрешение (без учёта ролей).
     *
     * @param string $permission
     * @return bool
     */
    public function hasDirectPermission(string $permission): bool
    {
        return $this->permissions->contains('guard_name', $permission);
    }

    /**
     * Проверка, пришло ли разрешение через хотя бы одну роль.
     *
     * @param Permission $permission
     * @return bool
     */
    public function hasPermissionThroughRole(Permission $permission): bool
    {
        return $permission->roles->intersect($this->roles)->isNotEmpty();
    }

    /**
     * Проверка, имеет ли пользователь разрешение напрямую или через роли.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermissionTo(string $permission): bool
    {
        $perm = Permission::query()
            ->where('guard_name', $permission)
            ->first();

        if (!$perm) {
            return false;
        }

        return $this->hasPermissionThroughRole($perm)
            || $this->hasDirectPermission($permission);
    }

    /**
     * Проверка, есть ли хотя бы одно из указанных разрешений.
     *
     * @param string[] $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        $direct = $this->permissions->pluck('guard_name')->toArray();
        $viaRoles = $this->roles->flatMap->permissions->pluck('guard_name')->toArray();

        return count(array_intersect($permissions, array_merge($direct, $viaRoles))) > 0;
    }

    /**
     * Проверка, есть ли все указанные разрешения.
     *
     * @param string[] $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions): bool
    {
        $direct = $this->permissions->pluck('guard_name')->toArray();
        $viaRoles = $this->roles->flatMap->permissions->pluck('guard_name')->toArray();

        return empty(array_diff($permissions, array_merge($direct, $viaRoles)));
    }

    /**
     * Получить разрешения по guard_name.
     *
     * @param string[] $permissions
     * @return Permission|Collection
     */
    protected function getAllPermissions(array $permissions): Permission|Collection
    {
        return Permission::whereIn('guard_name', $permissions)->get();
    }

    /**
     * Получить роли по title.
     *
     * @param string[] $roles
     * @return Role|Collection
     */
    protected function getAllRoles(array $roles): Role|Collection
    {
        return Role::whereIn('title', $roles)->get();
    }

    /**
     * Назначить пользователю одно или несколько разрешений.
     *
     * @param string[] $permissions
     * @return static
     */
    public function givePermissionsTo(...$permissions): static
    {
        $resolved = $this->getAllPermissions($permissions);
        $this->permissions()->saveMany($resolved);
        return $this;
    }

    /**
     * Удалить одно или несколько разрешений у пользователя.
     *
     * @param string[] $permissions
     * @return static
     */
    public function deletePermissions(...$permissions): static
    {
        $resolved = $this->getAllPermissions($permissions);
        $this->permissions()->detach($resolved);
        return $this;
    }

    /**
     * Обновить все разрешения пользователя (удалить старые, назначить новые).
     *
     * @param string[] $permissions
     * @return static
     */
    public function refreshPermissions(...$permissions): static
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo(...$permissions);
    }

    /**
     * Назначить пользователю одну или несколько ролей.
     *
     * @param string[] $roles
     * @return static
     */
    public function giveRolesTo(...$roles): static
    {
        $resolved = $this->getAllRoles($roles);
        $this->roles()->saveMany($resolved);
        return $this;
    }

    /**
     * Удалить одну или несколько ролей у пользователя.
     *
     * @param string[] $roles
     * @return static
     */
    public function deleteRoles(...$roles): static
    {
        $resolved = $this->getAllRoles($roles);
        $this->roles()->detach($resolved);
        return $this;
    }

    /**
     * Обновить все роли пользователя (удалить старые, назначить новые).
     *
     * @param string[] $roles
     * @return static
     */
    public function refreshRoles(...$roles): static
    {
        $this->roles()->detach();
        return $this->giveRolesTo(...$roles);
    }
}
