<?php

namespace App\Models\Operator;

use App\Models\ProductLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'operator_id'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ProductLogs::class);
    }
}
