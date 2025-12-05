<?php

namespace App\Contracts\DTOs;

use Illuminate\Pagination\CursorPaginator;

interface FromCursorPaginatorInterface
{
    /**
     * Создать DTO из cursor-пагинатора
     *
     * @param CursorPaginator $paginator Пагинатор
     * @return static Экземпляр DTO
     */
    public static function fromCursorPaginator(CursorPaginator $paginator): static;
}
