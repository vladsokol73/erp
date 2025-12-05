<?php

namespace App\DTO;

use App\Contracts\DTOs\CursorPaginationInterface;
use App\Contracts\DTOs\FromCursorPaginatorInterface;
use Illuminate\Pagination\CursorPaginator;

/**
 * DTO для бесконечной прокрутки
 */
class InfiniteScrollDto implements CursorPaginationInterface, FromCursorPaginatorInterface
{
    public readonly array $items;

    public readonly string|null $nextCursor;

    public readonly bool $hasMore;

    public function __construct(array $items, ?string $nextCursor, bool $hasMore)
    {
        $this->items = $items;
        $this->nextCursor = $nextCursor;
        $this->hasMore = $hasMore;
    }

    public static function fromCursorPaginator(CursorPaginator $paginator, ?callable $itemTransformer = null): static
    {
        $items = $paginator->items();
        $dtoItems = $itemTransformer ? array_map($itemTransformer, $items) : $items;

        return new self(
            items: $dtoItems,
            nextCursor: $paginator->nextCursor()?->encode(),
            hasMore: $paginator->hasMorePages()
        );
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getNextCursor(): ?string
    {
        return $this->nextCursor;
    }

    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    public static function empty(): static
    {
        return new self([], null, false);
    }
}
