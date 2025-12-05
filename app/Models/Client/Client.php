<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'fd_a',
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

    public function scopeSearch($query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('clickid', 'like', "%{$search}%")
                ->orWhere('tg_id', 'like', "%{$search}%")
                ->orWhere('c2d_channel_id', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        });
    }

    public function scopeForUserAvailableChannels(Builder $query, ?User $user): Builder
    {
        $available = $user->available_channels ?? null;

        // Если у пользователя нет доступных каналов — исключаем всех клиентов
        if ($available === null) {
            return $query->whereRaw('0 = 1');
        }

        // Если доступны все каналы — не фильтруем
        if (in_array('all', $available, true)) {
            return $query;
        }

        // Фильтруем клиентов по списку доступных каналов
        return $query->whereIn('c2d_channel_id', $available);
    }

    public function logs(): HasMany
    {
        // Связь: один Client имеет много ClientsLog по tg_id => client_id
        return $this->hasMany(ClientsLog::class, 'client_id', 'tg_id');
    }
}
