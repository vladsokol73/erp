<?php

namespace App\Models\Operator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'entity_type',
        'date',
        'new_client_chats',
        'total_clients',
        'inbox_messages',
        'outbox_messages',
        'start_time',
        'end_time',
        'total_time',
        'reg_count',
        'dep_count'
    ];
}
