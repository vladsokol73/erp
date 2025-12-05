<?php

namespace App\Http\Controllers;

use App\Contracts\Security\ApiKeyValidator;
use App\Contracts\User\UserRegistrar;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class ServerApiController extends Controller
{
    public function __construct(
        private readonly ApiKeyValidator $apiKeyValidator,
        private readonly UserRegistrar $userRegistrar
    ) {
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $apiKey = $request->header('X-Authorization-Key');

        if (!$this->apiKeyValidator->isValid($apiKey)) {
            return ApiResponse::unauthorized('Unauthorized: invalid API key.');
        }

        try {
            $user = $this->userRegistrar->register($request->validated());
            return ApiResponse::created([
                'message' => 'User created',
                'name' => $user->name,
                'id' => $user->id,
            ]);
        } catch (\Throwable $exception) {
            return ApiResponse::serverError($exception->getMessage());
        }
    }
}


