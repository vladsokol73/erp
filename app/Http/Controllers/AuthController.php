<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInFormRequest;
use App\Services\User\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(SignInFormRequest $request): JsonResponse
    {
        $result = $this->authService->attemptLogin($request->validated());

        return response()->json($result);
    }

    public function verify2FA(Request $request): JsonResponse
    {
        $result = $this->authService->verify2FACode($request->code);

        return response()->json($result);
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout();

        return Inertia::render('Auth/Login');
    }
}
