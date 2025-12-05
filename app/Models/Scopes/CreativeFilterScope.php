<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CreativeFilterScope implements Scope
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function apply(Builder $builder, Model $model): void
    {
        $this->applyCountryFilter($builder);
        $this->applyUserFilter($builder);
        $this->applyTypeFilter($builder);
        $this->applySearchFilter($builder);
        $this->applyTagsFilter($builder);
    }

    protected function applyCountryFilter(Builder $builder): void
    {
        if (!empty($this->filters['country'])) {
            $builder->whereIn('country_id', (array) $this->filters['country']);
        }
    }

    protected function applyTagsFilter(Builder $builder): void
    {
        if (!empty($this->filters['tag'])) {
            $tags = (array) $this->filters['tag']; // Приводим к массиву

            $builder->whereHas('tags', function (Builder $query) use ($tags) {
                $query->whereIn('tags.id', $tags);
            });
        }
    }

    protected function applyUserFilter(Builder $builder): void
    {
        if (!empty($this->filters['user'])) {
            $builder->whereIn('user_id', (array) $this->filters['user']);
        }
    }

    protected function applyTypeFilter(Builder $builder): void
    {
        if (!empty($this->filters['type'])) {
            $builder->whereIn('type', (array) $this->filters['type']);
        }
    }

    protected function applySearchFilter(Builder $builder): void
    {
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $builder->where(function ($q) use ($search) {
                // Существующая логика поиска
                $q->whereHas('country', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('creatives.created_at', 'like', '%' . $search . '%')
                    // Исправленный поиск по имени файла (без расширения)
                    ->orWhereRaw("
                SPLIT_PART(
                    SPLIT_PART(url, '/', array_length(string_to_array(url, '/'), 1)),
                    '.', 1
                ) ILIKE ?", ["%{$search}%"]);
            });
        }
    }
}
