<?php

namespace App\Contracts\DTOs;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

interface FromModelWithUserInterface
{
    /**
     * Создать DTO из модели с учётом пользователя
     *
     * @param Model $model Модель
     * @param User|null $user Пользователь
     * @return static Экземпляр DTO
     */
    public static function fromModelWithUser(Model $model, ?User $user = null): static;
}
