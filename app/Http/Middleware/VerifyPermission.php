<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;

class VerifyPermission
{
    protected $auth;
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $permission): mixed
    {
        if (!$this->auth->check()) {
            return redirect()->route('login');
        }
        
        if ($this->auth->user()->hasPermissionTo($permission)) {
            return $next($request);
        }
        
        abort(403);
    }
}
