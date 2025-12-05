<?php

namespace App\Facades;

use App\Services\GuardService;
use App\Services\User\PermissionService;
use App\Services\User\RoleService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static GuardService getService()
 * @method static PermissionService getPermissionService()
 * @method static RoleService getRoleService()
 * @method static bool all(array $rules, ?\App\Models\User\User $user = null)
 * @method static bool any(array $rules, ?\App\Models\User\User $user = null)
 * @method static mixed resolve(array $rules, ?\App\Models\User\User $user = null, mixed $default = null)
 */
class Guard extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'guard-service';
    }

    public static function permission(): PermissionService
    {
        /** @var GuardService $service */
        $service = static::getFacadeRoot();
        return $service->getPermissionService();
    }

    public static function role(): RoleService
    {
        /** @var GuardService $service */
        $service = static::getFacadeRoot();
        return $service->getRoleService();
    }
}
