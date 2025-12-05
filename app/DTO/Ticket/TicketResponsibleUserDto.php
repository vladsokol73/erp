<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromArrayInterface;
use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketResponsibleUser;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketResponsibleUserDto implements FromModelInterface, FromCollectionInterface, FromArrayInterface
{
    public function __construct(
        public readonly ?string $responsible_title,
        public readonly ?string $responsible_model_name,
        public readonly ?int $responsible_id
    ) {}

    public function toArray(): array
    {
        return [
            'responsible_title'      => $this->responsible_title,
            'responsible_model_name' => $this->responsible_model_name,
            'responsible_id'         => $this->responsible_id,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketResponsibleUser)) {
            throw new \InvalidArgumentException('Expected TicketResponsibleUser instance');
        }

        // Получаем связанную модель вручную
        $relatedTitle = null;
        $relatedName = null;

        match ($model->responsible_type) {
            User::class => $model->loadMissing('user'),
            Role::class => $model->loadMissing('role'),
            Permission::class => $model->loadMissing('permission'),
            default => null,
        };

        match ($model->responsible_type) {
            User::class => $relatedTitle = optional($model->user()->withTrashed()->first())->name,
            Role::class => $relatedTitle = $model->role->title,
            Permission::class => $relatedTitle = $model->permission->title,
            default => null,
        };

        // Извлекаем только имя модели без namespace
        $relatedName = class_basename($model->responsible_type);

        return new self(
            responsible_title: $relatedTitle,
            responsible_model_name: $relatedName,
            responsible_id: $model->responsible_id
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            responsible_title: $data['responsible_title'] ?? null,
            responsible_model_name: $data['responsible_model_name'] ?? null,
            responsible_id: isset($data['responsible_id']) ? (int) $data['responsible_id'] : null,
        );
    }


    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
