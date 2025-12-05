<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'currency'
    ];
}
