<?php

namespace App\Models\Ticket;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTopic extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'category_id',
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

        static::deleting(function ($topic) {
            $topic->formFields()->delete();
            $topic->tickets()->delete();
        });
    }

    /**
     * Get the category that owns the topic.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * Get the form fields for the topic.
     */
    public function formFields()
    {
        return $this->belongsToMany(TicketFormField::class, 'ticket_form_field_topic')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('ticket_form_field_topic.sort_order');
    }


    /**
     * Get available statuses
     */
    public function statuses()
    {
        $category = $this->category()->first();
        return $category->statuses();
    }

    /**
     * Get the tickets for this topic.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'topic_id');
    }

    /**
     * Get the responsible users for the topic.
     */
    public function responsibleUsers(): HasMany
    {
        return $this->hasMany(TicketResponsibleUser::class, 'source_id')
            ->where('source', 'topic');
    }

    public function approvalUsers(): HasMany
    {
        return $this->hasMany(TicketResponsibleUser::class, 'source_id')
            ->where('source', 'topic_approval');
    }

    public function allResponsibleUsers(): HasMany
    {
        return $this->hasMany(TicketResponsibleUser::class, 'source_id')
            ->whereIn('source', ['topic', 'topic_approval']);
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
