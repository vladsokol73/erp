<?php

namespace App\Models\Operator;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'client_id',
        'channel_id',
        'is_new_client',
        'event_type',
        'event_time'
    ];

    public static function startTime($operatorId, $date)
    {
        return self::where('operator_id', $operatorId)
            ->whereDate('event_time', $date)
            ->where('event_type', 'outbox')
            ->orderBy('event_time', 'asc')
            ->first()
            ->event_time ?? null;
    }

    public static function endTime($operatorId, $date)
    {
        return self::where('operator_id', $operatorId)
            ->whereDate('event_time', $date)
            ->where('event_type', 'outbox')
            ->orderBy('event_time', 'desc')
            ->first()
            ->event_time ?? null;
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'operator_id', 'operator_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }
}
