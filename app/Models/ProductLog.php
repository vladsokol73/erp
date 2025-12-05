<?php

namespace App\Models;

use App\Models\Operator\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'status',
        'c2d_channel_id',
        'tg_id',
        'prod_id',
        'dep_sum',
        'operator_id'
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::where('operator_id', $this->operator_id));
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $like = "%{$search}%";
        return $query
            ->where('player_id', 'ilike', $like)
            ->orWhere('tg_id', 'ilike', $like);
    }
}
