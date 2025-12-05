<?php

namespace App\Models\Ticket;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TicketStatus extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'is_default',
        'is_final',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_final' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the categories that use this status.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(TicketCategory::class, 'ticket_category_statuses', 'status_id', 'category_id')
            ->withPivot('is_default', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Get the tickets with this status.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}
