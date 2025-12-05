<?php

namespace App\Traits;

use App\Models\User\Permission;
use App\Models\User\Role;

trait HasRolesAndPermissions
{
    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * @param mixed $role
     * @return bool
     */
    public function hasRole(mixed $role)
    {
        if ($this->roles->contains('title', $role)) {
            return true;
        }
        return false;
    }

    /**
     * @param $permission
     * @return bool
     */
    protected function hasPermission($permission)
    {
        return (bool)$this->permissions->where('guard_name', $permission)->count();
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::query()->where('guard_name', $permission)->firstOrFail();
        }
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission->guard_name);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $permissions
     * @return mixed
     */
    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('guard_name', $permissions)->get();
    }

    /**
     * @param mixed ...$permissions
     * @return $this
     */
    public function givePermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    /**
     * @param mixed ...$permissions
     * @return $this
     */
    public function deletePermissions(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }

    /**
     * @param mixed ...$permissions
     * @return HasRolesAndPermissions
     */
    public function refreshPermissions(...$permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }

    public function canSeeAllComments(): bool {
        return $this->hasPermission('creatives.comments');
    }
}
