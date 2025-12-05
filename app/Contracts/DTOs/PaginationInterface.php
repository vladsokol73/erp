<?php

namespace App\Contracts\DTOs;

interface PaginationInterface
{
    /**
     * Получить текущую страницу
     *
     * @return int Номер текущей страницы
     */
    public function getCurrentPage(): int;

    /**
     * Получить общее количество страниц
     *
     * @return int Номер последней страницы
     */
    public function getLastPage(): int;

    /**
     * Получить общее количество элементов
     *
     * @return int Общее количество элементов
     */
    public function getTotal(): int;

    /**
     * Получить количество элементов на страницу
     *
     * @return int Количество элементов на страницу
     */
    public function getPerPage(): int;
}
