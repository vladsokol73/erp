<?php

namespace App\Models\Operator;

use App\Models\clients\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRetentionReport extends Model
{
    protected $table = 'ai_retention_reports';

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'operator_id',
        'score',
        'comment',
        'analysis',
        'raw_payload',
        'created_at',
        'conversation_date',
    ];

    protected $casts = [
        'score' => 'integer',
        'created_at' => 'datetime',
        'conversation_date' => 'date',
        'raw_payload' => 'array',
    ];

    // Связь с клиентом
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Связь с оператором
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn(Builder $q) => $q->whereDate('conversation_date', '>=', $from))
            ->when($to, fn(Builder $q) => $q->whereDate('conversation_date', '<=', $to));
    }
}
