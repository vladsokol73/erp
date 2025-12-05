<?php

namespace App\Models\Ticket;

use App\Models\Comment;
use App\Models\Scopes\TicketFilterScope;
use App\Models\Traits\HasTicketNumber;
use App\Models\User\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Ticket extends Model
{
    use SoftDeletes, HasTicketNumber;

    protected $fillable = [
        'ticket_number',
        'topic_id',
        'user_id',
        'status_id',
        'priority',
        'result'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TicketFilterScope);
    }

    // Мутатор для шифрования перед сохранением
    public function setResultAttribute($result): void
    {
        $this->attributes['result'] = Crypt::encryptString($result);
    }

    // Аксессор для расшифровки при извлечении
    public function getResultAttribute($result): bool|string|null
    {
        if (is_null($result)) {
            return null; // Если значение null, просто вернуть null
        }

        try {
            return Crypt::decryptString($result);
        } catch (DecryptException $e) {
            return $result; // Если не удалось расшифровать, вернуть как есть
        }
    }

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /**
     * Get the topic that owns the ticket.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(TicketTopic::class, 'topic_id');
    }

    /**
     * Get the user that created the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the field values for this ticket.
     */
    public function fieldValues(): HasMany
    {
        return $this->hasMany(TicketFieldValue::class, 'ticket_id');
    }

    /**
     * Get ticket statuses
     */
    public function statuses($user = null)
    {
        return $this->topic->category->statuses->filter(function ($status) use ($user) {
            // Если пользователь не передан, возвращаем все статусы
            if (!$user) {
                return true;
            }

            $isApproval = $this->topic->approval()
                ->where(function ($q) use ($user) {
                    $q->where('responsible_type', 'user')
                        ->where('value', $user->id)
                        ->orWhere(function ($q) use ($user) {
                            $q->where('responsible_type', 'role')
                                ->whereIn('value', $user->roles->pluck('id'));
                        })
                        ->orWhere(function ($q) use ($user) {
                            $q->where('responsible_type', 'permission')
                                ->whereIn('value', $user->permissions->pluck('id'));
                        });
                })->exists();

            $isResponsible = $this->topic->responsibleUsers()
                ->where(function ($q) use ($user) {
                    $q->where('responsible_type', 'user')
                        ->where('value', $user->id)
                        ->orWhere(function ($q) use ($user) {
                            $q->where('responsible_type', 'role')
                                ->whereIn('value', $user->roles->pluck('id'));
                        })
                        ->orWhere(function ($q) use ($user) {
                            $q->where('responsible_type', 'permission')
                                ->whereIn('value', $user->permissions->pluck('id'));
                        });
                })->exists();

            // Если пользователь одновременно approval и responsible
            if ($isApproval && $isResponsible) {
                return $status->is_final == 1 || $status->sort_order != 0;
            }

            // Если юзер - approval, исключаем статусы с sort_order == 0
            if ($isApproval) {
                return $status->sort_order != 0;
            }

            // Если юзер - responsible, оставляем только статусы с is_final == 1
            if ($isResponsible) {
                return $status->is_final == 1;
            }

            // Если юзер не относится ни к одной группе, ничего не возвращаем
            return false;
        });
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    /**
     * Get the logs for this ticket.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class, 'ticket_id');
    }

    /**
     * Get the comments for this ticket.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
