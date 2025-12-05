<?php

namespace App\Models\clients;

use App\Models\Scopes\ClientFilterScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickid',
        'tg_id',
        'source_id',
        'prod_id',
        'player_id',
        'reg',
        'dep',
        'redep',
        'reg_date',
        'dep_date',
        'redep_date',
        'dep_sum',
        'is_pb',
        'is_pb_date',
        'pb_bot_name',
        'pb_last_mssg',
        'pb_channelsub',
        'pb_channelsub_date',
        'is_c2d',
        'is_c2d_date',
        'c2d_channel_id',
        'c2d_tags',
        'c2d_last_mssg',
        'geo_click',
        'lang',
        'type',
        'user_agent',
        'oc',
        'ver_oc',
        'model',
        'browser',
        'ip',
        'sub1',
        'sub2',
        'sub3',
        'sub4',
        'sub5',
        'sub6',
        'sub7',
        'sub8',
        'sub9',
        'sub10',
        'sub11',
        'sub12',
        'sub13',
        'sub14',
        'sub15',
        'c2d_client_id',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new ClientFilterScope(request()->all()));
    }
}
