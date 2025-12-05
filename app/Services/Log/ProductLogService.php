<?php

namespace App\Services\Log;

use App\DTO\Log\ProductLogListDto;
use App\DTO\PaginatedListDto;
use App\Models\ProductLog;

class ProductLogService
{
    public function checkInProductLogsWithPlayerId(int $player_id, string $field, int|float $value): bool
    {
        return ProductLog::query()
            ->where('player_id', $player_id)
            ->where($field, $value)
            ->exists();
    }

    public function getProductLogsPaginated(int $page = 1, string $search = '', int $perPage = 10): PaginatedListDto
    {
        $query = ProductLog::query()
            ->search($search)
            ->orderByDesc('id');

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn($log) => ProductLogListDto::fromModel($log)
        );
    }
}
