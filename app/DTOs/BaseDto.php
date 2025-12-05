<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromArrayInterface;
use App\Contracts\DTOs\ToArrayInterface;
use App\Contracts\DTOs\ToJsonInterface;

abstract class BaseDto implements ToArrayInterface, ToJsonInterface, FromArrayInterface
{
    /**
     * Конструктор базового DTO класса
     */
    public function __construct()
    {
    }

    /**
     * Преобразовать DTO в массив
     *
     * @return array Массив данных
     */
    abstract public function toArray(): array;

    /**
     * Преобразовать DTO в JSON
     *
     * @param int $options Опции JSON
     * @return string JSON строка
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Создать DTO из массива данных
     *
     * @param array $data Массив данных
     * @return static Экземпляр DTO
     */
    public static function fromArray(array $data): static
    {
        // Получаем параметры конструктора
        $reflection = new \ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            return new static();
        }

        $parameters = $constructor->getParameters();

        // Формируем аргументы для конструктора
        $args = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $args[$name] = $data[$name] ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
        }

        // Создаем новый экземпляр DTO
        return new static(...$args);
    }
}
