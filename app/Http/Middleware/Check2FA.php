<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class Check2FA
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Если 2FA не включен, пропускаем
        if (!$user || !$user->google2fa_enabled) {
            return $next($request);
        }
        
        // Если это страница проверки 2FA или отправка кода, пропускаем
        if ($request->is('2fa/verify') || $request->is('2fa/validate')) {
            return $next($request);
        }
        
        // Если код уже был проверен в этой сессии, пропускаем
        if ($request->session()->get('2fa_verified')) {
            return $next($request);
        }
        
        // Иначе редиректим на страницу проверки 2FA
        return redirect()->route('2fa.verify');
    }
}
