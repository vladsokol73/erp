<?php

namespace App\Models\Ticket;

use App\Models\Comment;
use App\Models\ProductLog;
use App\Models\Traits\HasPlayerTicketNumber;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PlayerTicket extends Model
{
    use HasFactory;
    use HasPlayerTicketNumber;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'status',
        'player_id',
        'type',
        'tg_id',
        'screen_url',
        'sum',
        'approved_at',
        'result',
        'is_valid_tg_id',
        'is_valid_sum'
    ];

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('ticket_number', $search)
                ->orWhere('status', 'ILIKE', "%{$search}%")
                ->orWhere('type', 'ILIKE', "%{$search}%");
        });
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the user that created the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        if (!empty($filters['statuses'])) {
            $statuses = is_array($filters['statuses']) ? $filters['statuses'] : [$filters['statuses']];
            $query->whereIn('status', $statuses);
        }

        if (!empty($filters['type'])) {
            $types = is_array($filters['type']) ? $filters['type'] : [$filters['type']];
            $query->whereIn('type', $types);
        }

        if (!empty($filters['player_id'])) {
            $playerIds = is_array($filters['player_id']) ? $filters['player_id'] : [$filters['player_id']];
            $query->whereIn('player_id', $playerIds);
        }

        if (!empty($filters['date']['from']) && !empty($filters['date']['to'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['date']['from'])->startOfDay(),
                Carbon::parse($filters['date']['to'])->endOfDay(),
            ]);
        } elseif (!empty($filters['date']['from'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['date']['from'])->startOfDay());
        } elseif (!empty($filters['date']['to'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['date']['to'])->endOfDay());
        }

        return $query;
    }

    public function scopeSort(Builder $query, ?string $sort = null): Builder
    {
        return match ($sort) {
            'date_asc' => $query->orderBy('created_at', 'asc'),
            'date_desc' => $query->orderBy('created_at', 'desc'),
            'number_asc' => $query->orderBy('ticket_number', 'asc'),
            'number_desc' => $query->orderBy('ticket_number', 'desc'),
            'status_asc' => $query->orderBy('status', 'asc'),
            'status_desc' => $query->orderBy('status', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    public function scopeForModerator($query, User $moderator)
    {
        return $query->whereHas('user', function ($q) use ($moderator) {
            $q->whereHas('apiTokens', function ($sub) use ($moderator) {
                $sub->whereIn('api_tokens.id', $moderator->apiTokens()->pluck('api_tokens.id'));
            });
        });
    }

    public function productLogs(): HasMany
    {
        return $this->hasMany(ProductLog::class, 'player_id', 'player_id');
    }
}
