<?php

namespace App\Models;

use App\Models\Scopes\CreativeFilterScope;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $type
 * @property string $url
 * @property int $user_id
 * @property int $country_id
 * @property string $code
 * @property string|null $resolution
 * @method static \Illuminate\Support\Facades\Auth auth()
 */
class Creative extends Model
{
    use HasFactory;
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

    public function unlike(): void
    {
        // Удаляем все лайки текущего пользователя для креатива
        $this->likes()->where('user_id', auth()->id())->delete();
    }

    public function positiveLikesCount(): int
    {
        // Получаем количество положительных лайков (value == 1)
        return $this->likes()->where('value', 1)->count();
    }

    public function negativeLikesCount(): int
    {
        // Получаем количество отрицательных лайков (value == -1)
        return $this->likes()->where('value', -1)->count();
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    protected function getCurrentUserId(): ?int
    {
        return auth()->id();
    }

    public function isFavorite(): bool
    {
        return $this->favorites()->where('user_id', $this->getCurrentUserId())->exists();
    }

    public function toggleFavorite(): bool
    {
        $favorite = $this->favorites()->where('user_id', $this->getCurrentUserId())->first();

        if ($favorite) {
            $favorite->delete();
            return false; // Удалён из избранного
        }

        $this->favorites()->create([
            'user_id' => $this->getCurrentUserId()
        ]);
        return true; // Добавлен в избранное
    }

    // Подключаем скоуп в модель
    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new CreativeFilterScope(request()->all()));
    }
}
