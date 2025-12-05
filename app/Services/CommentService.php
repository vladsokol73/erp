<?php

namespace App\Services;

use App\DTO\CommentDto;
use App\Models\Comment;
use App\Models\Creative;
use App\Models\Ticket\PlayerTicket;
use App\Models\Ticket\Ticket;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function getComments( Model $model ): array
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        return CommentDto::fromCollection(
            $model->comments()->orderBy('created_at', 'desc')->get()
        );
    }

    public function getUserComments(Model $model, ?User $user = null): array
    {
        if (!($model instanceof Creative)) {
            throw new \InvalidArgumentException('Expected Creative type model');
        }

        $user = $user ?? Auth::user();

        return CommentDto::fromCollection(
            $model->comments()
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function addComment(Model $model, string $text, ?int $userId = null): CommentDto
    {
        if (!($model instanceof Creative) && !($model instanceof Ticket) && !($model instanceof PlayerTicket)) {
            throw new \InvalidArgumentException('Expected Creative or Ticket or PlayerTicket type model');
        }

        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new \InvalidArgumentException('User ID is required for comment creation');
        }

        $comment = new Comment();
        $comment->user_id = $userId;
        $comment->comment = $text;

        $model->comments()->save($comment);

        return CommentDto::fromModel($comment->fresh(['user']));
    }

    public function deleteComment(int $commentId, ?User $user = null): void
    {
        $comment = Comment::findOrFail($commentId);
        $user = $user ?? Auth::user();

        if ($comment->user_id !== $user->id) {
            throw new \RuntimeException('You do not have permission to delete this comment.');
        }

        $comment->delete();
    }

    public function updateComment(int $commentId, string $text, ?User $user = null): CommentDto
    {
        $comment = Comment::findOrFail($commentId);
        $user = $user ?? Auth::user();

        if ($comment->user_id !== $user->id) {
            throw new \RuntimeException('You do not have permission to edit this comment.');
        }

        $comment->comment = $text;
        $comment->save();

        return CommentDto::fromModel($comment->fresh(['user']));
    }
}
