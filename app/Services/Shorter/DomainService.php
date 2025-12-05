<?php

namespace App\Services\Shorter;

use App\DTO\PaginatedListDto;
use App\DTO\Shorter\DomainCreateDto;
use App\DTO\Shorter\DomainDto;
use Illuminate\Support\Facades\Http;

class DomainService
{
    public function __construct(
        protected ?string $key = null,
        protected ?string $url = null
    ) {
        $this->key ??= config('shorter.api_key');
        $this->url ??= config('shorter.url');
    }

    public function getDomains(): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->key
            ])->timeout(5)->get($this->url . '/domains');

            if ($response->successful()) {
                return DomainDto::fromCollection($response->collect());
            }

            throw new \RuntimeException("Domain service error: {$response->status()}");
        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Failed to fetch domain list', 0, $e);
        }
    }

    public function getEnabledDomains(): array
    {
        return array_filter($this->getDomains(), fn(DomainDto $dto) => $dto->is_active);
    }

    public function getDomainsPaginated(int $page, string $search): PaginatedListDto
    {
        $allDomains = $this->getDomains();

        $filtered = array_filter($allDomains, function (DomainDto $domain) use ($search) {
            return str_contains(strtolower($domain->domain), strtolower($search));
        });

        return PaginatedListDto::fromArrayPagination(array_values($filtered), $page, 10);
    }

    public function createDomain(DomainCreateDto $dto): DomainDto
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->key
            ])->post($this->url . '/domains', [
                'domain' => $dto->domain,
                'redirect_url' => $dto->redirect_url
            ]);

            if ($response->failed()) {
                throw new \RuntimeException('Failed to create domain: ' . $response->body());
            }

            return DomainDto::fromArray($response->json());

        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Domain creation failed', 0, $e);
        }
    }

    public function editDomain(DomainCreateDto $dto): DomainDto
    {
        if ($dto->id === null) {
            throw new \InvalidArgumentException('ID is required for domain update');
        }

        try {
            $payload = [
                'redirect_url' => $dto->redirect_url,
            ];

            if (!is_null($dto->is_active)) {
                $payload['is_active'] = $dto->is_active;
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->key
            ])->put($this->url . "/domains/{$dto->id}", $payload);

            if ($response->failed()) {
                throw new \RuntimeException("Failed to update domain: " . $response->body());
            }

            return DomainDto::fromArray($response->json());

        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Domain update failed', 0, $e);
        }
    }

    public function deleteDomain(int $id): void
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->key
            ])->delete($this->url . "/domains/{$id}");

            if ($response->failed()) {
                throw new \RuntimeException("Failed to delete domain: " . $response->body());
            }
        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Domain deletion failed', 0, $e);
        }
    }
}
