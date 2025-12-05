<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $favoritable_id
 * @property string $favoritable_type
 */
class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favoriteable_id',
        'favorieteable_type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoritable()
    {
        return $this->morphTo();
    }
}
