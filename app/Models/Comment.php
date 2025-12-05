<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Comment extends Model
{
    protected $fillable = ['comment', 'user_id', 'commentable_id', 'commentable_type'];

    public function commentable()
    {
        return $this->morphTo();
    }

    // Мутатор для шифрования перед сохранением
    public function setCommentAttribute($comment): void
    {
        $this->attributes['comment'] = Crypt::encryptString($comment);
    }

    // Аксессор для расшифровки при извлечении
    public function getCommentAttribute($comment): bool|string
    {
        return Crypt::decryptString($comment);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getCreativesCommentsForUser(Authenticatable $user, LengthAwarePaginator $creatives): array
    {
        // Получаем все комментарии для креативов
        $all_comments = Comment::query()->where('commentable_type', Creative::class);

        // Проверяем, может ли пользователь видеть все комментарии
        if (!$user->canSeeAllComments()) {
            $all_comments = $all_comments->where('user_id', $user->id);
        }

        $all_comments = $all_comments->orderBy('id')->get();

        $comments = [];

        foreach ($creatives as $creative) {
            if (!isset($comments[$creative->id])) {
                $comments[$creative->id] = [];
            }

            // Теперь используем полиморфную связь, чтобы фильтровать комментарии для каждого креатива
            foreach ($all_comments as $comment) {
                // Проверяем, что commentable_id соответствует ID текущего креатива
                if ($comment->commentable_id == $creative->id) {
                    $comments[$creative->id][] = $comment;
                }
            }
        }

        return $comments;
    }
}
