<?php

namespace App\Models\Ticket;

use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TicketResponsibleUser extends Model
{
    protected $fillable = [
        'source',
        'source_id',
        'responsible_type',
        'responsible_id',
    ];

    /**
     * Полиморфная связь с ответственным (User, Role, Permission и др.)
     */
    public function responsible(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Полиморфная связь для approval (так же, как для responsible)
     */
    public function approval(): MorphTo
    {
        // 'responsible_type' — здесь будет тип для approval (например, App\Models\User\User)
        return $this->morphTo('approval', 'responsible_type', 'responsible_id');
    }

    /**
     * Типы источников (source) — если это нужно в интерфейсе или логике.
     */
    public static function getSourceTypes(): array
    {
        return [
            'topic' => 'Topic',
            'topic_approval' => 'Topic Approval',
            'ticket' => 'Ticket',
        ];
    }

    /**
     * Типы назначения — можно использовать для других enum'ов в будущем.
     */
    public static function getResponsibleTypes(): array
    {
        return [
            'primary' => 'Primary Responsible',
            'backup' => 'Backup Responsible',
            'observer' => 'Observer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'responsible_id');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'responsible_id');
    }
}
