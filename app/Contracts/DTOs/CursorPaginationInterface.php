<?php

namespace App\Contracts\DTOs;

interface CursorPaginationInterface
{
    /**
     * Есть ли ещё данные для загрузки
     *
     * @return bool
     */
    public function hasMore(): bool;

    /**
     * Получить курсор для следующего запроса
     *
     * @return string|null
     */
    public function getNextCursor(): ?string;

    /**
     * Получить элементы текущей порции
     *
     * @return array
     */
    public function getItems(): array;

    /**
     * Получить пустой объект
     *
     * @return static
     */
    public static function empty(): static;
}
