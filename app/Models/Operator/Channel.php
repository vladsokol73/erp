<?php

namespace App\Models\Operator;

use App\Models\Project;
use App\Models\Traits\HasFlags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Channel extends Model
{
    use HasFlags;

    protected $fillable = [
        'name',
        'channel_id'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function operators(): HasMany
    {
        return $this->hasMany(Operator::class, 'c2d_channel_id', 'c2d_channel_id');
    }

    public function operator(): HasOne
    {
        return $this->hasOne(Operator::class, 'c2d_channel_id', 'c2d_channel_id');
    }


    public function statistics(): HasMany
    {
        return $this->hasMany(OperatorChannelStatistic::class, 'channel_id', 'channel_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OperatorLog::class, 'channel_id', 'channel_id');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ILIKE', "%{$search}%");

            if (is_numeric($search)) {
                $q->orWhere('channel_id', (int) $search)
                    ->orWhere('id', (int) $search);
            }
        });
    }
}
