<?php

namespace App\Contracts\DTOs;

interface ToArrayInterface
{
    /**
     * Преобразовать DTO в массив
     *
     * @return array Массив данных
     */
    public function toArray(): array;
}
