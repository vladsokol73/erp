<?php

namespace App\Services;

use App\Models\Creative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LikeService
{
    public function like(Model $model, ?int $userId = null): void
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new \RuntimeException('User is not authorized');
        }

        $existingReaction = $model->likes()->where('user_id', $userId)->first();

        if ($existingReaction && $existingReaction->value == 1) {
            $existingReaction->delete();
        } else {
            $model->likes()->updateOrCreate(
                ['user_id' => $userId],
                ['value' => 1]
            );
        }
    }

    public function dislike(Model $model, ?int $userId = null): void
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new \RuntimeException('User is not authorized');
        }

        $existingReaction = $model->likes()->where('user_id', $userId)->first();

        if ($existingReaction && $existingReaction->value == -1) {
            $existingReaction->delete();
        } else {
            $model->likes()->updateOrCreate(
                ['user_id' => $userId],
                ['value' => -1]
            );
        }
    }

    public function getStats(Model $model): array
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        return [
            'likes' => $model->likes()->where('value', 1)->count(),
            'dislikes' => $model->likes()->where('value', -1)->count()
        ];
    }

    public function getUserInteraction(Model $model, ?int $userId = null): array
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return [
                'liked' => false,
                'disliked' => false
            ];
        }

        $userLike = $model->likes()->where('user_id', $userId)->first();

        $userLiked = $userLike && $userLike->value === 1;
        $userDisliked = $userLike && $userLike->value === -1;

        return [
            'liked' => $userLiked,
            'disliked' => $userDisliked
        ];
    }
}
