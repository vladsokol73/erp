<?php

namespace App\Traits;

use App\Models\User\Permission;
use App\Models\User\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasRole(mixed $role): bool
    {
        return $this->roles->contains('title', $role);
    }

    /**
     * Проверка, есть ли прямое разрешение у пользователя (без учёта ролей)
     */
    public function hasDirectPermission(string $permission): bool
    {
        return $this->permissions->contains('guard_name', $permission);
    }

    /**
     * Проверка, есть ли у пользователя разрешение — напрямую или через роль
     */
    public function hasPermissionTo($permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::query()
                ->where('guard_name', $permission)
                ->firstOrFail();
        }

        return $this->hasPermissionThroughRole($permission)
            || $this->hasDirectPermission($permission->guard_name);
    }

    /**
     * Проверка, пришло ли разрешение к пользователю через роль
     */
    public function hasPermissionThroughRole($permission): bool
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получить коллекцию разрешений по guard_name
     */
    protected function getAllPermissions(array $permissions): array
    {
        return Permission::whereIn('guard_name', $permissions)->get();
    }

    /**
     * Назначить пользователю одно или несколько разрешений
     */
    public function givePermissionsTo(...$permissions): static
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    /**
     * Удалить одно или несколько разрешений у пользователя
     */
    public function deletePermissions(...$permissions): static
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }

    /**
     * Удалить все старые разрешения и выдать новые
     */
    public function refreshPermissions(...$permissions): static
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo(...$permissions);
    }
}
