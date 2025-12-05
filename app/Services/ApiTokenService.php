<?php

namespace App\Services;

use App\DTO\ApiTokenCreateDto;
use App\DTO\ApiTokenDto;
use App\DTO\ApiTokenListDto;
use App\DTO\PaginatedListDto;
use App\Enums\Chat2DeskEventEnum;
use App\Models\ApiToken;
use App\Models\Log;
use Illuminate\Support\Facades\Http;

class ApiTokenService
{
    public function getApiToken(int $id): ApiToken
    {
        return ApiToken::query()->findOrFail($id);
    }

    public function getTokenByService(string $service): ?string
    {
        return ApiToken::query()
            ->where('service', $service)
            ->value('token');
    }

    public function getApiTokensPaginated(int $page, string $search, int $perPage): PaginatedListDto
    {
        $query = ApiToken::query()
        ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn($token) => ApiTokenListDto::fromModel($token)
        );
    }

    public function getAllC2DeskTokens():array
    {
        return ApiTokenDto::fromCollection(
            ApiToken::c2Desk()->get()
        );
    }

    public function createApiToken(ApiTokenCreateDto $dto): ApiTokenListDto
    {
        if ($dto->service == 'Chat2Desk') {
            $maxAttempts = 10;
            $attempt = 0;
            $statusClient = '';
            $statusOperator = '';

            // Формируем payload
            $payloadClient = [
                'url' => 'https://erp.investingindigital.com/api/webhook/c2d',
                'name' => 'ERP-prod',
                'events' => Chat2DeskEventEnum::clientEvents(),
                'channels' => [],
            ];

            $payloadOperator = [
                'url' => 'https://erp.investingindigital.com/api/c2d/operator',
                'name' => 'ERP-operator',
                'events' => ['inbox', 'outbox'],
                'channels' => [],
            ];

            while ($attempt < $maxAttempts) {
                $attempt++;

                $statusClient = $this->registerWebhook($dto->token, $payloadClient);
                $statusOperator = $this->registerWebhook($dto->token, $payloadOperator);

                if ($statusClient === 'success' && $statusOperator === 'success') {
                    break;
                }

                sleep(1);
            }

            if ($statusClient === 'success' && $statusOperator === 'success') {
                $token = ApiToken::query()->create($dto->toArray());
                return ApiTokenListDto::fromModel($token);
            }
        } else {
            $token = ApiToken::query()->create($dto->toArray());
            return ApiTokenListDto::fromModel($token);
        }

        throw new \RuntimeException('Failed to register one or both webhooks after 10 attempts.');
    }

    public function editApiToken(string $email, int $id): ApiTokenListDto
    {
        $token  = $this->getApiToken($id);

        $token->update(['email' => $email]);

        return ApiTokenListDto::fromModel($token);
    }

    public function deleteApiToken(int $id): void
    {
        $apiToken = $this->getApiToken($id);

        try {
            if ($apiToken->service == 'Chat2Desk') {
                $response = Http::withHeaders([
                    'Authorization' => $apiToken->token,
                ])->get('https://api.chat2desk.com/v1/webhooks');

                $webhooks = $response->json()['data'] ?? [];

                foreach ($webhooks as $hook) {
                    if (in_array($hook['name'], ['ERP-operator', 'ERP-prod'])) {
                        Http::withHeaders([
                            'Authorization' => $apiToken->token,
                        ])->delete("https://api.chat2desk.com/v1/webhooks/{$hook['id']}");

                        $apiToken->delete();
                    } else {
                        Log::error('Webhook was not deleted: ');
                    }
                }
            } else {
                $apiToken->delete();
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Failed to delete token or webhooks: ' . $e->getMessage());
        }
    }

    private function registerWebhook(string $token, array $payload): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.chat2desk.com/v1/webhooks', $payload);

            $data = $response->json();

            // Логируем ответ, если статус не success
            if (($data['status'] ?? '') !== 'success') {
                Log::error('Webhook registration failed: ' . json_encode($data) . ' HTTP Status: ' . $response->status());
            }

            return $data['status'] ?? '';
        } catch (\Throwable $e) {
            return '';
        }
    }
}
