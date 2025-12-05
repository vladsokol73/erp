<?php

namespace App\Services\User;

use App\DTO\PaginatedListDto;
use App\DTO\User\UserCreateDto;
use App\DTO\User\UserDto;
use App\DTO\User\UserListDto;
use App\DTO\User\UserOperatorDto;
use App\Facades\Guard;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getUsers(): array
    {
        return UserDto::fromCollection(
            User::query()->get()
        );
    }

    public function getUserById(int $userId): User
    {
        return User::query()->findOrFail($userId);
    }

    public function getOperators(): array
    {
        $users = User::withRole('operator')->get();

        return UserOperatorDto::fromCollection($users);
    }

    public function getUsersPaginated(int $page, string $search): PaginatedListDto
    {
        return PaginatedListDto::fromPaginator(
            User::query()
                ->orderBy('id')
                ->search($search)
                ->paginate(perPage: 10, page: $page),
            fn ($user) => UserListDto::fromModel($user)
        );
    }

    public function getUsersWithCreatives(?User $user = null): array
    {
        $user ??= Auth::user();

        return UserDto::fromCollection(
            User::query()
                ->hasCreativesInAvailableCountries($user)
                ->hasCreativesWithAvailableTags($user)
                ->get()
        );
    }

    public function createUser(UserCreateDto $dto): UserListDto
    {
        // Создание пользователя
        $user = User::query()->create([
            'email' => $dto->email,
            'name' => $dto->name,
            'password' => Hash::make($dto->password),
            'available_countries' => $dto->available_countries ?? null,
            'available_tags' => $dto->available_tags ?? null,
            'available_channels' => $dto->available_channels,
            'available_operators' => $dto->available_operators,
        ]);

        // Присваивание роли
        $user->roles()->attach($dto->role_id);

        // Присваивание прав
        $user->permissions()->attach(
            array_column($dto->permissions, 'id')
        );

        // Присваивание api tokens (many-to-many)
        if (is_array($dto->api_token_ids) && count($dto->api_token_ids) > 0) {
            $user->apiTokens()->sync($dto->api_token_ids);
        }

        return UserListDto::fromModel($user);
    }

    public function editUser(int $userId, UserCreateDto $dto): UserListDto
    {
        $user = $this->getUserById($userId);

        $updateData = [
            'email' => $dto->email,
            'name' => $dto->name,
            'available_countries' => $dto->available_countries,
            'available_tags' => $dto->available_tags,
            'available_channels' => $dto->available_channels,
            'available_operators' => $dto->available_operators,
        ];

        // Обновляем пароль только если он передан
        if (!is_null($dto->password)) {
            $updateData['password'] = Hash::make($dto->password);
        }

        $user->update($updateData);

        // Обновляем роль
        $user->roles()->sync([$dto->role_id]);

        // Обновляем права
        $permissions_names = array_map(
            fn(array $p) => $p['name'],
            $dto->permissions
        );

        Guard::permission()->sync($user, $permissions_names);

        // Синхронизация api tokens (many-to-many)
        if (is_array($dto->api_token_ids)) {
            $user->apiTokens()->sync($dto->api_token_ids);
        }

        return UserListDto::fromModel($user);
    }

}
