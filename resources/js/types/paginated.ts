declare namespace App.DTO {
    interface PaginatedListDto<T> {
        items: T[];
        currentPage: number;
        lastPage: number;
        perPage: number;
        total: number;
    }

    interface InfiniteScrollDto<T> {
        items: T[];
        nextCursor: string | null;
        hasMore: boolean;
    }
}
