<?php

namespace App\Contracts\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;

interface FromPaginatorInterface
{
    /**
     * Создать DTO из пагинатора
     *
     * @param LengthAwarePaginator $paginator Пагинатор
     * @return static Экземпляр DTO
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): static;
}
