<?php

namespace App\DTO\User;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class UserCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $name,
        public readonly int $role_id,

        /* @var PermissionDto[] */
        public readonly array $permissions = [],

        public readonly array|null $available_countries = null,
        public readonly array|null $available_tags = null,
        public readonly array|null $available_channels = null,
        public readonly array|null $available_operators = null,

        public readonly string|null $email = null,
        public readonly string|null $password,
        /** @var int[]|null */
        public readonly array|null  $api_token_ids = null,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->string('name'),
            role_id: $request->integer('role_id'),
            permissions: $request->input('permissions', []),
            available_countries: $request->input('available_countries', []),
            available_tags: $request->input('available_tags', []),
            available_channels: $request->input('available_channels', []),
            available_operators: $request->input('available_operators', []),
            email: $request->string('email'),
            password: $request->string('password'),
            api_token_ids: $request->input('api_token_ids'),
        );
    }

    public function toArray(): array
    {
        return [
            'name'                => $this->name,
            'password'            => $this->password,
            'role_id'             => $this->role_id,
            'permissions'         => $this->permissions,
            'available_countries' => $this->available_countries,
            'available_tags'      => $this->available_tags,
            'available_channels'  => $this->available_channels,
            'available_operators' => $this->available_operators,
            'email'               => $this->email,
            'api_token_ids'       => $this->api_token_ids,
        ];
    }
}
