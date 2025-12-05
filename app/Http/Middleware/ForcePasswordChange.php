<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Account\PasswordPolicyService;

class ForcePasswordChange
{
    public function __construct(private readonly PasswordPolicyService $policy) {}

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Если не авторизован — пропускаем
        if (!$user) {
            return $next($request);
        }

        // Если это страница смены пароля или logout — пропускаем (чтобы не зациклиться)
        if ($request->is('force-password-change')
            || $request->is('force-password-change/*')
            || $request->is('logout')) {
            return $next($request);
        }

        // Если флаг установлен — редиректим на смену пароля
        if ($this->policy->mustForceChange($user)) {
            return redirect()->route('password.force.show');
        }

        return $next($request);
    }
}
