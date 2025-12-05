<?php

namespace App\Support;

use App\Models\User\User;
use App\Services\User\PermissionService;
use App\Services\User\RoleService;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Описание одного условия доступа.
 *
 * Используется GuardService для комбинированной проверки.
 */
class AccessRule
{
    /**
     * Условие доступа (fn(User): bool)
     */
    protected Closure $condition;

    /**
     * Callback, если доступ разрешён (fn(): mixed)
     */
    protected ?Closure $callback;

    /**
     * @param Closure $condition Условие (обязательное)
     * @param Closure|null $callback Действие или значение при успехе (опционально)
     */
    public function __construct(Closure $condition, ?Closure $callback = null)
    {
        $this->condition = $condition;
        $this->callback = $callback;
    }

    /**
     * Условие по разрешению.
     *
     * @param string|array $permissions
     * @param Closure|null $callback
     * @return static
     */
    public static function permission(string|array $permissions, ?Closure $callback = null): static
    {
        return new static(
            function (User $user) use ($permissions): bool {
                $service = app(PermissionService::class);
                return is_array($permissions)
                    ? $service->hasAnyPermission($permissions, $user)
                    : $service->hasPermission($permissions, null, $user);
            },
            $callback
        );
    }

    /**
     * Условие по роли.
     *
     * @param string|array $roles
     * @param Closure|null $callback
     * @return static
     */
    public static function role(string|array $roles, ?Closure $callback = null): static
    {
        return new static(
            function (User $user) use ($roles): bool {
                $service = app(RoleService::class);
                return is_array($roles)
                    ? $service->hasAnyRole($roles, $user)
                    : $service->hasRole($roles, $user);
            },
            $callback
        );
    }

    /**
     * Условие с произвольной логикой.
     *
     * @param Closure $check fn(User): bool
     * @param Closure|null $callback
     * @return static
     */
    public static function condition(Closure $check, ?Closure $callback = null): static
    {
        return new static($check, $callback);
    }

    /**
     * Проверить условие доступа.
     *
     * @param \App\Models\User\User|null $user
     * @return bool
     */
    public function passes(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user ? call_user_func($this->condition, $user) : false;
    }

    /**
     * Получить результат callback'а или true при успешной проверке.
     *
     * @return mixed
     */
    public function resolve(): mixed
    {
        return $this->callback
            ? call_user_func($this->callback)
            : true;
    }
}
