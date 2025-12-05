<?php

namespace App\DTO\User;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
        #[LiteralTypeScriptType('RoleDto')]
        public readonly RoleDto $role,
        public readonly bool|null $two_factor = null,
        public readonly array $available_countries,
        public readonly array $available_channels,
        public readonly array $available_operators,
        public readonly array $available_tags,
        #[LiteralTypeScriptType('Array<PermissionDto>')]
        public readonly array $permissions,
        public readonly string|null $last_login,
        public readonly string|null $password = null,
        /** @var int[] */
        public readonly array $api_token_ids = [],
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof User)) {
            throw new \InvalidArgumentException('Expected User type model');
        }

        return new self(
            id: $model->id,
            email: $model->email,
            name: $model->name,
            role: RoleDto::fromModel($model->roles()->first()),
            two_factor: $model->google2fa_enabled,
            available_countries: $model->available_countries ?? [],
            available_channels: $model->available_channels ?? [],
            available_operators: $model->available_operators ?? [],
            available_tags: $model->available_tags ?? [],
            permissions: PermissionDto::fromCollection($model->permissions),
            last_login: $model->last_login_at ? Carbon::parse($model->last_login_at)->format("Y-m-d") : null,
            api_token_ids: $model->apiTokens()->pluck('api_tokens.id')->toArray(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
