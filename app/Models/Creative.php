<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Creative extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'url',
        'user_id',
        'country_id',
        'code',
        'resolution',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function hasTag($tag): bool
    {
        return (bool)$this->tags()->where('tags.id', $tag)->count();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function liked(): bool
    {
        // Проверяем, есть ли лайк от текущего пользователя для этого креатива с value == 1
        return $this->likes()->where('user_id', auth()->id())->where('value', 1)->exists();
    }

    public function disliked(): bool
    {
        // Проверяем, есть ли дизлайк от текущего пользователя для этого креатива с value == -1
        return $this->likes()->where('user_id', auth()->id())->where('value', -1)->exists();
    }

    public function like(): void
    {
        // Находим существующий лайк для текущего пользователя и креатива
        $existingLike = $this->likes()->where('user_id', auth()->id())->first();

        if ($existingLike && $existingLike->value == 1) {
            // Если лайк уже стоит, снимаем его
            $existingLike->delete();
        } else {
            // Если лайка нет или он был снят, ставим новый лайк
            $this->likes()->updateOrCreate(
                ['user_id' => auth()->id()],
                ['value' => 1]
            );
        }
    }

    public function dislike(): void
    {
        // Находим существующий дизлайк для текущего пользователя и креатива
        $existingDislike = $this->likes()->where('user_id', auth()->id())->first();

        if ($existingDislike && $existingDislike->value == -1) {
            // Если дизлайк уже стоит, снимаем его
            $existingDislike->delete();
        } else {
            // Если дизлайка нет или он был снят, ставим новый дизлайк
            $this->likes()->updateOrCreate(
                ['user_id' => auth()->id()],
                ['value' => -1]
            );
        }
    }


    // Подключаем скоуп в модель
    public static function boot(): void
    {
        parent::boot();

        //static::addGlobalScope(new CreativeFilterScope(request()->all()));
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->orWhere('code', $search)
                ->orWhereHas('country', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });
        });
    }

    public function scopeFavorite(Builder $query, bool|null $isFavorite, User $user): Builder
    {
        if ($isFavorite === null) {
            return $query;
        }

        return $query->whereHas('favorites', function ($q) use ($user) {
            $q->where('user_id', $user?->id);
        }, $isFavorite ? '>' : '=', 0);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        if (!empty($filters['countries'])) {
            $query->whereIn('country_id', $filters['countries']);
        }

        if (!empty($filters['users'])) {
            $query->whereIn('user_id', $filters['users']);
        }

        if (!empty($filters['types'])) {
            $query->whereIn('type', $filters['types']);
        }

        if (!empty($filters['tags'])) {
            $tagIds = $filters['tags'];
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            }, '=', count($tagIds));
        }

        return $query;
    }

    public function scopeSort(Builder $query, string $sortBy = 'date_desc'): Builder
    {
        switch ($sortBy) {
            case 'date_asc':
                return $query->orderBy('created_at', 'asc');

            case 'likes_positive':
                return $query->withCount(['likes' => function ($query) {
                    $query->where('value', 1);
                }])->orderBy('likes_count', 'desc');

            case 'likes_negative':
                return $query->withCount(['likes' => function ($query) {
                    $query->where('value', -1);
                }])->orderBy('likes_count', 'desc');

            case 'date_desc':
            default:
                return $query->orderBy('created_at', 'desc');
        }
    }

    public function scopeForUserAvailableCountries(Builder $query, ?User $user): Builder
    {
        $available = $user->available_countries ?? null;

        // Нет доступных стран — исключаем всё
        if ($available === null) {
            return $query->whereRaw('0 = 1');
        }

        // Все страны доступны — фильтр не нужен
        if (in_array('all', $available, true)) {
            return $query;
        }

        return $query->whereIn('country_id', $available);
    }


    public function scopeForUserAvailableTags(Builder $query, ?User $user): Builder
    {
        $available = $user->available_tags ?? null;

        // Если нет доступных тегов — исключить все креативы
        if ($available === null) {
            return $query->whereRaw('0 = 1');
        }

        // Если доступны все теги — не фильтруем
        if (in_array('all', $available, true)) {
            return $query;
        }

        // Убираем креативы, у которых есть хотя бы один тег вне доступных
        return $query->whereDoesntHave('tags', function ($tagQuery) use ($available) {
            $tagQuery->whereNotIn('tags.id', $available);
        });
    }

    public function creativeStatistic()
    {
        return CreativeStatistic::query()->where('code', $this->code)->orderBy('date', 'desc')->first();
    }
}
