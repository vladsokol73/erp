<?php

namespace App\Services;

use App\Models\Flag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FlagService
{
    /**
     * Добавить флаг модели
     */
    public function addFlag(Model $model, string $flagName): void
    {
        $flag = Flag::firstOrCreate(['name' => $flagName]);
        $model->flags()->syncWithoutDetaching($flag);
    }

    /**
     * Удалить флаг у модели
     */
    public function removeFlag(Model $model, string $flagName): void
    {
        $flag = Flag::where('name', $flagName)->first();
        if ($flag) {
            $model->flags()->detach($flag);
        }
    }

    /**
     * Проверить наличие флага у модели
     */
    public function hasFlag(Model $model, string $flagName): bool
    {
        return $model->flags->contains('name', $flagName);
    }

    /**
     * Получить все модели указанного типа с флагом
     *
     * @param class-string<Model> $modelClass
     */
    public function getModelsWithFlag(string $modelClass, string $flagName): Collection
    {
        $flag = Flag::where('name', $flagName)->first();

        if (!$flag) {
            return collect();
        }

        return $flag->morphedByMany($modelClass, 'flaggable')->get();
    }
}
