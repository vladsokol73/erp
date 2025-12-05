<?php

namespace App\Services\Client;

use App\DTO\Client\ClientDetailsDto;
use App\DTO\Client\ClientListDto;
use App\DTO\Client\ClientLogDto;
use App\DTO\PaginatedListDto;
use App\Models\Client\Client;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    /**
     * Получение одного клиента по ID
     */
    public function getClient(int $clientId): Client
    {
        return Client::query()->findOrFail($clientId);
    }

    /**
     * Получение логов клиента по ID
     */
    public function getClientLogsDto(int $clientId): array
    {
        $client = $this->getClient($clientId);

        return ClientLogDto::fromCollection($client->logs);
    }
    /**
     * Получение одного клиента в виде детализации DTO
     */
    public function getClientDetailsDto(int $clientId): ClientDetailsDto
    {
        $client = $this->getClient($clientId);
        return ClientDetailsDto::fromModel($client);
    }

    /**
     * Получение клиентов с пагинацией и поиском
     */
    public function getClientsPaginated(int $page, string $search, int $perPage, User|null $user = null): PaginatedListDto
    {
        $user = $user ?? Auth::user();

        $query = Client::query()
            ->forUserAvailableChannels($user)
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn(Client $client) => ClientListDto::fromModel($client)
        );
    }
}
