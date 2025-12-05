<?php

namespace App\Models;

use App\Models\Scopes\HasCreativesWithCountryFilter;
use App\Models\Scopes\HasCreativesWithTagFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Tag extends Model
{
    use HasCreativesWithCountryFilter;
    use HasCreativesWithTagFilter;

    protected $fillable = ['name', 'style'];

    public function creatives(): BelongsToMany
    {
        return $this->belongsToMany(Creative::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ILIKE', "%{$search}%");
        });
    }
}
