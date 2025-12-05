<?php

namespace App\Contracts\DTOs;

interface ToJsonInterface
{
    /**
     * Преобразовать DTO в JSON
     *
     * @param int $options Опции JSON
     * @return string JSON строка
     */
    public function toJson(int $options = 0): string;
}
