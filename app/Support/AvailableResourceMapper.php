<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class AvailableResourceMapper
{
    /**
     * @param array|string|null $ids
     * @param class-string<Model> $modelClass
     * @return array
     */
    public static function mapNames(array|string|null $ids, string $modelClass): array
    {
        if (empty($ids)) {
            return [];
        }

        if (is_array($ids) && count($ids) === 1 && $ids[0] === 'all') {
            return ['all'];
        }

        if ($ids === 'all') {
            return ['all'];
        }

        return $modelClass::query()
            ->whereIn('id', $ids)
            ->pluck('name')
            ->all();
    }
}
