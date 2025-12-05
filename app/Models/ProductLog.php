<?php

namespace App\Models;

use App\Models\Operator\Operator;
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
        'operator_id',
        'currency'
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::where('operator_id', $this->operator_id));
    }
}
