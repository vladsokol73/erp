<?php

namespace App\Models\Operator;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'entity_type',
        'date',
        'new_client_chats',
        'total_clients',
        'inbox_messages',
        'outbox_messages',
        'start_time',
        'end_time',
        'total_time',
        'reg_count',
        'dep_count'
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'entity_id', 'operator_id');
    }

    public function scopeForUserAvailableOperators(Builder $query, ?User $user): Builder
    {
        $available = $user->available_operators ?? null;

        if ($available === null) {
            return $query->whereRaw('0 = 1');
        }

        if (in_array('all', $available, true)) {
            return $query;
        }

        return $query->whereIn('entity_id', $available);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        // Фильтр по дате
        if (!empty($filters['date']['from']) && !empty($filters['date']['to'])) {
            $query->whereBetween('date', [
                Carbon::parse($filters['date']['from'])->startOfDay(),
                Carbon::parse($filters['date']['to'])->endOfDay(),
            ]);
        } elseif (!empty($filters['date']['from'])) {
            $query->whereDate('date', '>=', Carbon::parse($filters['date']['from'])->startOfDay());
        } elseif (!empty($filters['date']['to'])) {
            $query->whereDate('date', '<=', Carbon::parse($filters['date']['to'])->endOfDay());
        }

        if (!empty($filters['operators'])) {
            $ids = $filters['operators'];
            $invert = $filters['filter_mode_operators'] ?? 'off';
            $query->{$invert === 'on' ? 'whereNotIn' : 'whereIn'}('entity_id', $ids);
        }

        if (!empty($filters['channels'])) {
            $ids = $filters['channels'];
            $invert = $filters['filter_mode_channels'] ?? 'off';
            $query->{$invert === 'on' ? 'whereNotIn' : 'whereIn'}('channel_id', $ids);
        }


        return $query;
    }


    public function scopeOnlyOperators(Builder $query): Builder
    {
        return $query->where('entity_type', 'operator');
    }
}
