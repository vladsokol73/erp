<?php

namespace App\Models\User;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory, HasRolesAndPermissions;

    protected $fillable = [
        'title',
        'description',
        'guard_name'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }
}
