<?php

namespace App\Models\Ticket;

use App\Models\TicketTopic;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponsibleUser extends Model
{
    protected $fillable = [
        'source',
        'source_id',
        'responsible_type',
        'value'
    ];

    protected $casts = [
        'workload_limit' => 'integer'
    ];

    /**
     * Get the responsible user based on source type.
     */
    public function responsibleUser(): ?BelongsTo
    {
        return match ($this->responsible_type) {
            'user' => $this->belongsTo(User::class, 'value'),
            'role' => $this->belongsTo(Role::class, 'value'),
            'permission' => $this->belongsTo(Permission::class, 'value'),
            default => null,
        };
    }

    /**
     * Get available responsible types with their labels.
     */
    public static function getResponsibleTypes(): array
    {
        return [
            'primary' => 'Primary Responsible',
            'backup' => 'Backup Responsible',
            'observer' => 'Observer'
        ];
    }

    /**
     * Get available source types with their labels.
     */
    public static function getSourceTypes(): array
    {
        return [
            'user' => 'User',
            'role' => 'Role',
            'department' => 'Department'
        ];
    }
}
