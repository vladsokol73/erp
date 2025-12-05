<?php

namespace App\Models;

use App\Models\Operator\Channel;
use App\Models\Operator\Operator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Flag extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Получить все модели, у которых есть этот флаг
     */
    public function operators(): MorphToMany
    {
        return $this->morphedByMany(Operator::class, 'flaggable');
    }

    public function channels(): MorphToMany
    {
        return $this->morphedByMany(Channel::class, 'flaggable');
    }
}
