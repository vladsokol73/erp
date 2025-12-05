<?php

namespace App\Models\Operator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorChannelStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'channel_id',
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
