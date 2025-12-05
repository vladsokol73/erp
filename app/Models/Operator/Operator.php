<?php

namespace App\Models\Operator;

use App\Models\ProductLog;
use App\Models\Traits\HasFlags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operator extends Model
{
    use HasFlags;

    protected $fillable = [
        'name',
        'operator_id'
    ];

    public function aiRetentionReports(): HasMany
    {
        return $this->hasMany(AiRetentionReport::class, 'operator_id', 'operator_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductLog::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(OperatorStatistic::class, 'entity_id', 'operator_id')
            ->where('entity_type', 'operator');
    }

    public function channelStatistics(): HasMany
    {
        return $this->hasMany(OperatorChannelStatistic::class, 'operator_id', 'operator_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OperatorLog::class, 'operator_id', 'operator_id');
    }
}
