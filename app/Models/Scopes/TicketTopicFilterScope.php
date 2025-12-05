<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Request;

class TicketTopicFilterScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if ($category = Request::get('category')) {
            $builder->where('category_id', $category);
        }

        if (Request::get('active') === '1') {
            $builder->where('is_active', true);
        }
    }
}
