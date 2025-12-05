<?php

namespace App\Services;

use App\DTO\ChannelDto;
use App\DTO\ChannelListDto;
use App\DTO\PaginatedListDto;
use App\Models\Operator\Channel;

class ChannelService
{
    public function getChannels(): array
    {
        return ChannelDto::fromCollection(
            Channel::query()->get(['channel_id', 'name'])
        );
    }

    public function getChannelById(int $channelId): Channel
    {
        return Channel::query()->findOrFail($channelId);
    }

    public function getChannelsPaginated(int $page, string $search, int $perPage): PaginatedListDto
    {
        $query = Channel::query()
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn($operator) => ChannelListDto::fromModel($operator)
        );
    }

    public function editChannel(string $name, int $channelId, bool $hasAiRetention): ChannelListDto
    {
       $channel = $this->getChannelById($channelId);
       $channel->name = $name;
       $channel->setFlag('ai_retention', $hasAiRetention);
       $channel->save();

       return ChannelListDto::fromModel($channel);
    }

    public function deleteChannel(int $channelId): void
    {
        $channel = $this->getChannelById($channelId);
        $channel->delete();
    }
}
