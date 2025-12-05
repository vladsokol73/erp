<?php

namespace App\Http\Controllers\Admin;

use App\DTO\ApiTokenCreateDto;
use App\Enums\ApiServiceEnum;
use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApiTokenCreateRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ApiTokenService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApiTokenController extends Controller
{
    public function __construct(
        public readonly ApiTokenService $apiTokenService,
    ) {}

    public function showApiTokens(Request $request): Response
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');
        $perPage = $request->integer('perPage', 10);

        return Inertia::render('AdminPanel/ApiTokens', [
            'tokens' => $this->apiTokenService->getApiTokensPaginated(page: $page, search: $search, perPage: $perPage),
            'services' => ApiServiceEnum::values(),
        ]);
    }

    public function createApiToken(ApiTokenCreateRequest $request): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $dto = ApiTokenCreateDto::fromRequest($request);

            return ApiResponse::success(
                [
                    "token" => $this->apiTokenService->createApiToken($dto),
                ],
                'ApiToken successfully created.'
            );
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create ApiToken.');
        }
    }

    public function editApiToken(Request $request, int $id): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $validated = $request->validate([
                'email' => 'required|email|max:64',
            ]);

            return ApiResponse::success(
                [
                    "token" => $this->apiTokenService->editApiToken(email: $validated['email'], id: $id)
                ],
                'Token successfully updated.'
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Api Token not found.');
        }
    }

    public function deleteApiToken(int $id): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $this->apiTokenService->deleteApiToken($id);

            return ApiResponse::success('Token successfully deleted.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Api Token not found.');
        }
    }
}
