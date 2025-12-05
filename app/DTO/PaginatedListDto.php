<?php

namespace App\DTO;


use App\Contracts\DTOs\FromPaginatorInterface;
use App\Contracts\DTOs\PaginationInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

class PaginatedListDto implements PaginationInterface, FromPaginatorInterface
{
    public readonly array $items;

    public readonly int $currentPage;

    public readonly int $lastPage;

    public readonly int $perPage;

    public readonly int $total;

    public function __construct(
        array $items,
        int $currentPage,
        int $lastPage,
        int $perPage,
        int $total
    ) {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->lastPage = $lastPage;
        $this->perPage = $perPage;
        $this->total = $total;
    }

    public static function fromPaginator(
        LengthAwarePaginator $paginator,
        callable|null $itemTransformer = null
    ): static {
        $items = $paginator->items();
        $dtoItems = array_map($itemTransformer, $items);

        return new self(
            items: $dtoItems,
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total()
        );
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public static function empty(): static
    {
        return new self(
            items: [],
            currentPage: 1,
            lastPage: 1,
            perPage: 16,
            total: 0
        );
    }

    public static function fromArrayPagination(
        array $items,
        int $page,
        int $perPage = 10
    ): static {
        $total = count($items);
        $slicedItems = array_slice($items, ($page - 1) * $perPage, $perPage);

        return new self(
            items: array_values($slicedItems),
            currentPage: $page,
            lastPage: max(1, (int) ceil($total / $perPage)),
            perPage: $perPage,
            total: $total
        );
    }
}
