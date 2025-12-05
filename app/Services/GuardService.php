<?php

namespace App\Services;

use App\Models\User\User;
use App\Services\User\PermissionService;
use App\Services\User\RoleService;
use App\Support\AccessRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Сервис проверки доступа пользователя.
 *
 * Позволяет выполнять проверки по ролям, разрешениям,
 * а также по множеству AccessRule с логикой any/all/resolve.
 */
class GuardService
{
    /**
     * Сервисы для ролей и разрешений.
     *
     * @param \App\Services\User\PermissionService $permissionService
     * @param RoleService $roleService
     */
    public function __construct(
        protected readonly \App\Services\User\PermissionService $permissionService,
        protected readonly RoleService                          $roleService,
    ) {}


    /**
     * Вернуть PermissionService
     */
    public function getPermissionService(): \App\Services\User\PermissionService
    {
        return $this->permissionService;
    }

    /**
     * Вернуть RoleService
     */
    public function getRoleService(): RoleService
    {
        return $this->roleService;
    }

    /**
     * Проверка: принадлежит ли указанный ресурс пользователю.
     *
     * @param Model $model
     * @param User|null $user
     * @param string $ownerKey — название поля с ID владельца, по умолчанию 'user_id'
     * @return bool
     */
    public function owns(Model $model, ?User $user = null, string $ownerKey = 'user_id'): bool
    {
        $user = $user ?? Auth::user();

        if (!$user || !$model->getAttribute($ownerKey)) {
            return false;
        }

        return $model->getAttribute($ownerKey) === $user->getKey();
    }

    /**
     * Проверка: все правила должны быть выполнены.
     *
     * @param AccessRule[] $rules
     * @param User|null $user
     * @return bool
     */
    public function all(array $rules, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        foreach ($rules as $rule) {
            if (!$rule instanceof AccessRule || !$rule->passes($user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверка: хотя бы одно из правил выполнено.
     *
     * @param AccessRule[] $rules
     * @param User|null $user
     * @return bool
     */
    public function any(array $rules, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        foreach ($rules as $rule) {
            if ($rule instanceof AccessRule && $rule->passes($user)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Выполнить первое подходящее правило и вернуть результат его callback'а.
     *
     * @param AccessRule[] $rules
     * @param \App\Models\User\User|null $user
     * @param mixed|null $default
     * @return mixed
     */
    public function resolve(array $rules, ?User $user = null, mixed $default = null): mixed
    {
        $user = $user ?? Auth::user();

        foreach ($rules as $rule) {
            if ($rule instanceof AccessRule && $rule->passes($user)) {
                return $rule->resolve();
            }
        }

        return is_callable($default) ? $default() : $default;
    }
}
