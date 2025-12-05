<?php

namespace App\Models;

use App\Models\Operator\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'currency',
        'country_id'
    ];

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
