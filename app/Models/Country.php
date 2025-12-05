<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
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
