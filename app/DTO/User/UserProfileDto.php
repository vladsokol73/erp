<?php

namespace App\DTO\User;

use App\Contracts\DTOs\FromModelInterface;
use App\Models\Country;
use App\Models\Operator\Channel;
use App\Models\Operator\Operator;
use App\Models\Tag;
use App\Models\User\User;
use App\Support\AvailableResourceMapper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserProfileDto implements FromModelInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
        #[LiteralTypeScriptType('RoleDto')]
        public readonly RoleDto $role,
        public readonly bool|null $two_factor = null,
        public readonly array $available_countries = [],
        public readonly array $available_channels = [],
        public readonly array $available_operators = [],
        public readonly array $available_tags = [],
        #[LiteralTypeScriptType('Array<PermissionDto>')]
        public readonly array $permissions = [],
        public readonly string|null $last_login_at = null,
        public readonly string|null $timezone = null,
        public readonly bool $telegram_connected = false,
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
            available_countries: AvailableResourceMapper::mapNames($model->available_countries, Country::class),
            available_channels: AvailableResourceMapper::mapNames($model->available_channels, Channel::class),
            available_operators: AvailableResourceMapper::mapNames($model->available_operators, Operator::class),
            available_tags: AvailableResourceMapper::mapNames($model->available_tags, Tag::class),
            permissions: PermissionDto::fromCollection($model->permissions),
            last_login_at: $model->last_login_at
                ? Carbon::parse($model->last_login_at)->format("Y-m-d H:i:s")
                : null,
            timezone: $model->timezone,
            telegram_connected: $model->activeTelegramIntegrations(),
            api_token_ids: $model->apiTokens()->pluck('api_tokens.id')->toArray(),
        );
    }
}
