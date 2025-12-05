<?php

namespace App\Models\Operator;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Channel extends Model
{

    protected $fillable = [
        'name',
        'channel_id'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
