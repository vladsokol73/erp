<?php

namespace App\Models\Ticket;

use App\Models\Comment;
use App\Models\Traits\HasTicketNumber;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
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
        'result',
        'approved_at',
        'closed_at',
    ];

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

    /**
     * Поиск по номеру тикета и значениям полей
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('ticket_number', $search)
            ->orWhereHas('fieldValues', function ($q) use ($search) {
                $q->where('value', 'ILIKE', "%{$search}%");
            });
        });
    }


    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        // Применение фильтра по типу, без сортировки
        if (!empty($filters['type'])) {
            $query = $this->applySort($query, $filters['type']);
        }

        if (!empty($filters['statuses'])) {
            $query->whereIn('status_id', $filters['statuses']);
        }

        if (!empty($filters['topics'])) {
            $query->whereIn('topic_id', $filters['topics']);
        }

        if (!empty($filters['categories'])) {
            $query->whereHas('topic', function ($q) use ($filters) {
                $q->whereIn('category_id', $filters['categories']);
            });
        }

        if (!empty($filters['date']['from']) && !empty($filters['date']['to'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['date']['from'])->startOfDay(),
                Carbon::parse($filters['date']['to'])->endOfDay(),
            ]);
        } elseif (!empty($filters['date']['from'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['date']['from'])->startOfDay());
        } elseif (!empty($filters['date']['to'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['date']['to'])->endOfDay());
        }

        return $query;
    }

    protected function applySort(Builder $query, string $sort): Builder
    {
        switch ($sort) {
            case 'new':
                return $query->whereHas('status', fn ($q) => $q->where('is_default', true));

            case 'approval':
                return $query->whereHas('status', fn ($q) => $q->where('is_approval', true));

            case 'closed':
                return $query->whereHas('status', fn ($q) => $q->where('is_final', true));

            case 'todo':
                return $query->whereHas('status', function ($q) {
                    $q->where('is_final', false)
                        ->where(function ($sub) {
                            $sub->where('is_default', false)
                                ->where('is_approval', false);
                        });
                });

            case 'all':
            default:
                return $query;
        }
    }

    public function scopeSort(Builder $query, ?string $sort = null): Builder
    {
        return match ($sort) {
            'date_asc' => $query->orderBy('created_at', 'asc'),
            'date_desc' => $query->orderBy('created_at', 'desc'),
            'priority_asc' => $query->orderBy('priority', 'asc'),
            'priority_desc' => $query->orderBy('priority', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }


    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для тикетов, модерируемых пользователем
     */
    public function scopeModeratedBy(Builder $query, User $user): Builder
    {
        $userId = $user->id;
        $roleIds = $user->roles->pluck('id')->toArray();
        $permissionIds = $user->permissions->pluck('id')->toArray();

        return $query->where(function (Builder $query) use ($userId, $roleIds, $permissionIds) {

            // Для approval-модераторов
            $query->orWhere(function (Builder $q) use ($userId, $roleIds, $permissionIds) {
                $q->whereHas('topic.approvalUsers', function (Builder $subQ) use ($userId, $roleIds, $permissionIds) {
                    $subQ->where(function (Builder $where) use ($userId, $roleIds, $permissionIds) {
                        $where->where(function ($q) use ($userId) {
                            $q->where('responsible_type', User::class)
                                ->where('responsible_id', $userId);
                        });

                        if (!empty($roleIds)) {
                            $where->orWhere(function ($q) use ($roleIds) {
                                $q->where('responsible_type', Role::class)
                                    ->whereIn('responsible_id', $roleIds);
                            });
                        }

                        if (!empty($permissionIds)) {
                            $where->orWhere(function ($q) use ($permissionIds) {
                                $q->where('responsible_type', Permission::class)
                                    ->whereIn('responsible_id', $permissionIds);
                            });
                        }
                    });
                })
                    ->whereHas('status', function (Builder $statusQ) {
                        $statusQ->where('is_default', false)
                            ->where('is_approval', true)
                            ->where('is_final', false);
                    });
            });

            // Для responsible-модераторов
            $query->orWhere(function (Builder $q) use ($userId, $roleIds, $permissionIds) {
                $q->whereHas('topic.responsibleUsers', function (Builder $subQ) use ($userId, $roleIds, $permissionIds) {
                    $subQ->where(function (Builder $where) use ($userId, $roleIds, $permissionIds) {
                        $where->where(function ($q) use ($userId) {
                            $q->where('responsible_type', User::class)
                                ->where('responsible_id', $userId);
                        });

                        if (!empty($roleIds)) {
                            $where->orWhere(function ($q) use ($roleIds) {
                                $q->where('responsible_type', Role::class)
                                    ->whereIn('responsible_id', $roleIds);
                            });
                        }

                        if (!empty($permissionIds)) {
                            $where->orWhere(function ($q) use ($permissionIds) {
                                $q->where('responsible_type', Permission::class)
                                    ->whereIn('responsible_id', $permissionIds);
                            });
                        }
                    });
                })
                    ->whereHas('status', function (Builder $statusQ) {
                        $statusQ->where('is_default', false)
                            ->where('is_approval', false);
                    });
            });
        });
    }

}
