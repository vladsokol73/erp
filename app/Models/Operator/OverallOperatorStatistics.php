<?php

namespace App\Models\Operator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverallOperatorStatistics extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_new_client_chats',
        'total_clients',
        'total_inbox_messages',
        'total_outbox_messages',
        'total_time',
        'total_reg_count',
        'total_dep_count',
        'top_operators',
        'top_channels'
    ];

    protected $casts = [
        'top_operators' => 'array',
        'top_channels' => 'array',
    ];
}
