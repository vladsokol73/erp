<?php

namespace App\Services\Shorter;

use App\DTO\PaginatedListDto;
use App\DTO\Shorter\UrlCreateDto;
use App\DTO\Shorter\UrlListDto;
use App\Models\ShortUrl;
use App\Models\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UrlService
{
    public function __construct(
        protected ?string $key = null,
        protected ?string $url = null
    ) {
        $this->key ??= config('shorter.api_key');
    }

    public function getUrl(int $id): ShortUrl
    {
        return ShortUrl::query()->findOrFail($id);
    }

    public function getUrlByCode(string $code): ShortUrl
    {
        return ShortUrl::query()
            ->where('short_code', $code)
            ->first();
    }

    public function getUserUrlsPaginated(int $page, string $search, User|null $user = null): PaginatedListDto
    {
        $user = $user ?? Auth::user();

        $query = ShortUrl::query()
            ->orderBy('created_at', 'desc')
            ->ownedBy(userId: $user->id)
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: 10, page: $page),
            fn($ticket) => UrlListDto::fromModel($ticket)
        );
    }

    public function checkUrlExist(string $code): bool
    {
        return ShortUrl::query()
            ->where('short_code', $code)
            ->exists();
    }

    public function createShortUrl(UrlCreateDto $dto, User|null $user = null): string
    {
        $user = $user ?? Auth::user();

        // Предварительная собственная проверка — (лучше оставить, но она не стопроцентная!)
        if ($dto->short_code && $this->checkUrlExist($dto->short_code)) {
            throw new \DomainException("This short code already exists");
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->key
            ])->post('https://' . $dto->domain . '/shorten', [
                'target_url' => $dto->original_url,
                'custom_code' => $dto->short_code
            ]);

            // Вытаскиваем полезную инфу из JSON-ответа python-сервиса
            $data = $response->json();

            // Если сервер вернул ошибку (в любом статусе, кроме 2xx)
            if ($response->failed()) {
                $data = $response->json();
                $message = $data['detail'] ?? "Failed to create short url";
                throw new \DomainException($message, $response->status());
            }

            if ($dto->short_code && (!empty($data['short_code']) && $data['short_code'] !== $dto->short_code)) {
                throw new \DomainException('This URL already exists: https://' . $dto->domain . '/' . $data['short_code']);
            }

            // Успешное создание и запись в свою БД
            ShortUrl::query()->create([
                'original_url' => $data['target_url'] ?? $dto->original_url,
                'short_code' => $data['short_code'],
                'domain' => $dto->domain,
                'user_id' => $user->id,
            ]);
            return 'https://' . $dto->domain . '/' . $data['short_code'];

        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Failed to create url: ' . $e->getMessage(), 0, $e);
        }
    }

    public function editShortUrl(UrlCreateDto $dto, User|null $user = null): UrlListDto
    {
        $user = $user ?? Auth::user();

        $url = ShortUrl::query()
            ->where('id', $dto->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        try {
            if ($url->domain !== $dto->domain) {
                $createResponse = Http::withHeaders([
                    'X-API-Key' => $this->key,
                    'Accept' => 'application/json',
                ])->post("https://{$dto->domain}/shorten", [
                    'target_url' => $dto->original_url,
                    'short_code' => $dto->short_code,
                ]);

                if ($createResponse->failed()) {
                    throw new \RuntimeException('Failed to create URL on new domain');
                }

                // Удаляем старую ссылку
                Http::withHeaders([
                    'X-API-Key' => $this->key,
                    'Accept' => 'application/json',
                ])->delete("https://{$url->domain}/urls/{$url->short_code}");

                $url->update([
                    'original_url' => $dto->original_url,
                    'short_code' => $dto->short_code,
                    'domain' => $dto->domain
                ]);

            } else {
                // Тот же домен — обновляем существующую
                $updatePayload = [];

                if ($url->original_url !== $dto->original_url) {
                    $updatePayload['target_url'] = $dto->original_url;
                }

                if ($url->short_code !== $dto->short_code) {
                    $updatePayload['short_code'] = $dto->short_code;
                }

                if (!empty($updatePayload)) {
                    $updateResponse = Http::withHeaders([
                        'X-API-Key' => $this->key,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->put("https://{$dto->domain}/urls/{$url->short_code}", $updatePayload);

                    if ($updateResponse->failed()) {
                        throw new \RuntimeException('Failed to update URL on current domain');
                    }
                }

                $url->update([
                    'original_url' => $dto->original_url,
                    'short_code' => $dto->short_code,
                    'domain' => $dto->domain
                ]);
            }

            return new UrlListDto(
                id: $url->id,
                original_url: $url->original_url,
                short_code: $url->short_code,
                domain: $url->domain,
                created_at: $url->created_at,
            );

        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException('URL not found or access denied', 0, $e);
        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Failed to edit URL', 0, $e);
        }
    }

    public function deleteShortUrl(int $id, User|null $user = null): void
    {
        $user = $user ?? Auth::user();

        $url = ShortUrl::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->key,
                'Accept' => 'application/json',
            ])->delete("https://{$url->domain}/urls/{$url->short_code}");

            if ($response->failed()) {
                throw new \RuntimeException('Failed to delete from external service');
            }

            $url->delete();
        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Failed to delete short URL', 0, $e);
        }
    }
}
