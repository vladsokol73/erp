<?php

namespace App\Models;

use App\Models\Scopes\HasCreativesWithCountryFilter;
use App\Models\Scopes\HasCreativesWithTagFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasCreativesWithCountryFilter;
    use HasCreativesWithTagFilter;

    public $timestamps = false;

    protected $fillable = [
      'name',
      'iso',
      'img'
    ];

    public function creatives(): HasMany
    {
        return $this->hasMany(Creative::class);
    }
}
