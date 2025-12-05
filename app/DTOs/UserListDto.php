<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserListDto extends BaseDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
        public readonly string $role,
        public readonly bool $two_factor,
        public readonly string|null $last_login,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->role,
            'two_factor' => $this->two_factor,
            'last_login' => $this->last_login,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof User)) {
            throw new \InvalidArgumentException('Ожидалась модель типа User');
        }

        return new self(
            id: $model->id,
            email: $model->email,
            name: $model->name,
            role: $model->roles()->first()->title,
            two_factor: $model->google2fa_enabled,
            last_login: $model->last_login_at ? Carbon::parse($model->last_login_at)->format("Y-m-d") : null,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($user) => static::fromModel($user))->toArray();
    }
}
