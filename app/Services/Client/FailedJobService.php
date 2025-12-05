<?php

namespace App\Services\Client;

use App\DTO\PaginatedListDto;
use App\DTO\Client\FailedJobListDto;
use App\Models\Client\FailedJob;

class FailedJobService
{
    public function getFailedJobsPaginated(int $page = 1, int $perPage = 10): PaginatedListDto
    {
        $query = FailedJob::query()->orderByDesc('failed_at');

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn (FailedJob $job) => FailedJobListDto::fromModel($job)
        );
    }
}
