<?php

namespace App\Models\Operator;

use App\Models\Client\Client;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRetentionReportTest extends Model
{
    protected $table = 'ai_retention_reports_test';

    public $timestamps = true;

    protected $fillable = [
        'client_id',
        'operator_id',
        'user_id',
        'score',
        'comment',
        'analysis',
        'raw_payload',
        'conversation_date',
        'prompt',
    ];

    protected $casts = [
        'score' => 'integer',
        'conversation_date' => 'date',
        'raw_payload' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query->orderBy('id', 'desc');
        }
        return $query
            ->where(function ($q) use ($search) {
                // поиск по user_id
                $q->where('user_id', $search)
                    // поиск по имени пользователя
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'ILIKE', "%{$search}%");
                    });
            })
            ->orderBy('id', 'desc');
    }
}



