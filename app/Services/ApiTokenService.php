<?php

namespace App\Services;

use App\Models\ApiToken;

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
}
