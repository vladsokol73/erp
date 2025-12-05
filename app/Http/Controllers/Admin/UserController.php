<?php

namespace App\Http\Controllers\Admin;


use App\DTO\Creative\TagDto;
use App\DTO\User\UserCreateDto;
use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserEditRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ApiTokenService;
use App\Services\ChannelService;
use App\Services\CountryService;
use App\Services\Creative\TagService;
use App\Services\Operator\OperatorService;
use App\Services\User\PermissionService;
use App\Services\User\RoleService;
use App\Services\User\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class UserController extends Controller
{
    public function __construct(
        public readonly UserService       $userService,
        public readonly CountryService    $countryService,
        public readonly RoleService       $roleService,
        public readonly OperatorService   $operatorService,
        public readonly ChannelService    $chanelService,
        public readonly PermissionService $permissionService,
        public readonly TagService        $tagService,
        public readonly ApiTokenService   $apiTokenService,
    ) {
    }

    public function showUsers(Request $request): Response
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        return Inertia::render('AdminPanel/Users',
        [
            'users' => $this->userService->getUsersPaginated(page: $page, search: $search),
            'countries' => $this->countryService->getCountries(),
            'roles' => $this->roleService->getRoles(),
            'permissions' => $this->permissionService->getPermissions(),
            'tags' => TagDto::fromCollection($this->tagService->getTags()),
            'operators' => $this->operatorService->getOperators(),
            'channels' => $this->chanelService->getChannels(),
            'api_tokens' => $this->apiTokenService->getAllC2DeskTokens(),
        ]);
    }

    public function createUser(UserCreateRequest $request): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        $validated = $request->validated();

        $userCreateDto = new UserCreateDto(
            name: $validated['name'],
            role_id: $validated['role_id'],
            permissions: $validated['permissions'],
            available_countries: $validated['available_countries'],
            available_tags: $validated['available_tags'],
            available_channels: $validated['available_channels'],
            available_operators: $validated['available_operators'],
            email: $validated['email'],
            password: $validated['password'],
            api_token_ids: $validated['api_token_ids'] ?? [],
        );

        try {
            return ApiResponse::created([
                'user' => $this->userService->createUser($userCreateDto),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create user.');
        }
    }

    public function editUser(UserEditRequest $request, int $userId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        $validated = $request->validated();

        $userEditDto = new UserCreateDto(
            name: $validated['name'],
            role_id: $validated['role_id'],
            permissions: $validated['permissions'],
            available_countries: $validated['available_countries'],
            available_tags: $validated['available_tags'],
            available_channels: $validated['available_channels'],
            available_operators: $validated['available_operators'],
            email: $validated['email'],
            password: $validated['password'],
            api_token_ids: $validated['api_token_ids'] ?? [],
        );

        try {
            return ApiResponse::created([
                'user' => $this->userService->editUser(userId: $userId, dto: $userEditDto),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to edit user.');
        }
    }

    public function deleteUser(int $userId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }
        try {
            $user = $this->userService->getUserById($userId);

            $user->delete();

            return ApiResponse::success('User successfully deleted.');
        } catch (Exception) {
            return ApiResponse::serverError('Failed to edit user.');
        }
    }
}
