<?php

namespace App\Services;

use App\Models\Creative;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    public function add(Model $model, ?int $userId = null): bool
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new \RuntimeException('User is not authorized');
        }

        // Проверяем, есть ли уже эта модель в избранном
        $exists = $model->favorites()->where('user_id', $userId)->exists();

        if (!$exists) {
            // Если модели нет в избранном, добавляем её
            $model->favorites()->create([
                'user_id' => $userId
            ]);
            return true;
        }

        return false;
    }

    public function remove(Model $model, ?int $userId = null): bool
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new \RuntimeException('User is not authorized');
        }

        // Находим запись в избранном
        $favorite = $model->favorites()->where('user_id', $userId)->first();

        if ($favorite) {
            // Если модель в избранном, удаляем её
            $favorite->delete();
            return true;
        }

        return false;
    }

    public function isFavorite(Model $model, ?int $userId = null): bool
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return false;
        }

        return $model->favorites()->where('user_id', $userId)->exists();
    }
}
