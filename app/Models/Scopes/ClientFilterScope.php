<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClientFilterScope implements Scope
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function apply(Builder $builder, Model $model): void
    {
        $this->applySearchFilter($builder);
    }

    protected function applySearchFilter(Builder $builder): void
    {
        if (!empty($this->filters['searchClient'])) {
            $search = $this->filters['searchClient'];

            // Преобразуем строку в число, если это возможно
            $numericSearch = is_numeric($search) ? (int) $search : $search;

            $builder->where(function ($q) use ($numericSearch) {
                $q->where('tg_id', '=', $numericSearch)
                    ->orWhere('clickid', '=', $numericSearch);
            });
        }
    }
}
