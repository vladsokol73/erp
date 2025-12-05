<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreativeStatistic extends Model
{
    protected $table = 'creative_statistic';

    protected $fillable = [
        'code',
        'clicks',
        'ctr',
        'leads',
        'date'
    ];

    public function creative(): BelongsTo
    {
        return $this->belongsTo(Creative::class, 'code', 'code');
    }
}
