<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    protected $fillable = ['value', 'user_id', 'likeable_id', 'likeable_type'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getCreativesLikes(LengthAwarePaginator $creatives) : array
    {
        $likes = [];
        foreach ($creatives as $creative) {
            if (!isset($likes[$creative->id])) {
                $likes[$creative->id] = [
                    'positive' => [],
                    'negative' => [],
                ];
            }

            // Загружаем все лайки для данного креатива через полиморфную связь
            $creativeLikes = $creative->likes;

            foreach ($creativeLikes as $like) {
                // Проверяем, если лайк положительный или отрицательный
                if ($like->value > 0) {
                    $likes[$creative->id]['positive'][] = $like;
                } elseif ($like->value < 0) {
                    $likes[$creative->id]['negative'][] = $like;
                }
            }
        }

        return $likes;
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePositive($query)
    {
        return $query->where('value', 1);
    }

    public function scopeNegative($query)
    {
        return $query->where('value', -1);
    }
}
