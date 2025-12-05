<?php

namespace App\Services\Ticket;

use App\DTO\PaginatedListDto;
use App\DTO\Ticket\TicketCategoriesListDto;
use App\DTO\Ticket\TicketCategoryCreateDto;
use App\DTO\Ticket\TicketCategoryDto;
use App\Models\Ticket\TicketCategory;
use App\Support\SlugGenerator;

class TicketCategoryService
{
    public function getCategory(int $categoryId): TicketCategory
    {
        return TicketCategory::query()->findOrFail($categoryId);
    }

    public function getCategories(): array
    {
        return TicketCategoryDto::fromCollection(TicketCategory::query()->get());
    }

    public function getCategoryPaginated(int $page, string $search): PaginatedListDto
    {
        $query = TicketCategory::query()
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: 10, page: $page),
            fn($tag) => TicketCategoriesListDto::fromModel($tag)
        );
    }

    public function createCategory(TicketCategoryCreateDto $dto): TicketCategory
    {
        $category = new TicketCategory([
            'name'        => $dto->name,
            'slug'        => SlugGenerator::generate($dto->name),
            'is_active'   => $dto->is_active,
            'description' => $dto->description,
            'sort_order'  => $dto->sort_order,
        ]);

        $category->save();

        // Привязываем статусы по ID
        $statusIds = collect($dto->statuses)
            ->pluck('id')
            ->filter()
            ->unique()
            ->toArray();

        $category->statuses()->sync($statusIds);

        return $category;
    }


    public function updateCategory(int $categoryId, TicketCategoryCreateDto $dto): TicketCategory
    {
        $category = $this->getCategory($categoryId);

        // Обновление полей категории
        $category->update([
            'name'        => $dto->name,
            'slug'        => SlugGenerator::generate($dto->name),
            'is_active'   => $dto->is_active,
            'description' => $dto->description,
            'sort_order'  => $dto->sort_order,
        ]);

        // Связываем статусы по ID
        $statusIds = collect($dto->statuses)
            ->pluck('id')
            ->filter()
            ->unique()
            ->toArray();

        $category->statuses()->sync($statusIds);

        return $category;
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = $this->getCategory($categoryId);
        $category->delete();
    }
}
