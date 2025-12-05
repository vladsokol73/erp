<?php

namespace App\Models\Ticket;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TicketCategory extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($category) {
            $category->topics()->delete();
        });
    }

    /**
     * Get the topics for the category.
     */
    public function topics(): HasMany
    {
        return $this->hasMany(TicketTopic::class, 'category_id');
    }

    /**
     * Get the statuses available for this category.
     */
    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(TicketStatus::class, 'ticket_category_statuses', 'category_id', 'status_id')
            ->withPivot('is_default', 'sort_order')
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    /**
     * Get the default status for this category.
     */
    public function defaultStatus()
    {
        return $this->statuses()->wherePivot('is_default', true)->first();
    }
}
