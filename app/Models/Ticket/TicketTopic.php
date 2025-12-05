<?php

namespace App\Models\Ticket;

use App\Models\Scopes\TicketTopicFilterScope;
use App\Models\Traits\HasSlug;
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
        'approval_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TicketTopicFilterScope);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($topic) {
            $topic->formFields()->delete();
            $topic->tickets()->delete();
            $topic->approval()->delete();
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
    public function formFields(): HasMany
    {
        return $this->hasMany(TicketFormField::class);
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

    /**
     * Get the approval users for the topic.
     */
    public function approval(): BelongsTo
    {
        return $this->BelongsTo(TicketResponsibleUser::class);
    }
}
