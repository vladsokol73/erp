<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'webhook_event',
        'webhook_data',
        'task_status',
        'worker_id',
        'started_at',
        'finished_at',
        'result',
        'c2d_client_id',
    ];

    public function client(): BelongsTo
    {
        // Связь: один ClientsLog принадлежит Client по client_id => tg_id
        return $this->belongsTo(Client::class, 'client_id', 'tg_id');
    }
}
