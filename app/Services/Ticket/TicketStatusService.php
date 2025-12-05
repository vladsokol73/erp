<?php

namespace App\Services\Ticket;

use App\DTO\PaginatedListDto;
use App\DTO\Ticket\TicketStatusCreateDto;
use App\DTO\Ticket\TicketStatusDto;
use App\DTO\Ticket\TicketStatusesListDto;
use App\Models\Ticket\TicketCategory;
use App\Models\Ticket\TicketStatus;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Model;

class TicketStatusService
{
    public function __construct(
        protected TicketCategoryService $categoryService,
    ) {}

    public function getStatus(int $statusId): TicketStatus
    {
        return TicketStatus::query()->findOrFail($statusId);
    }

    public function getDefaultStatus(int $categoryId): TicketStatus
    {
        $category = $this->categoryService->getCategory($categoryId);

        return $category->defaultStatus();
    }

    public function getStatuses( Model $model ): array
    {
        if (!($model instanceof TicketCategory)) {
            throw new \InvalidArgumentException('Expected TicketCategory type model');
        }

        return TicketStatusDto::fromCollection(
            $model->statuses()->orderBy('sort_order', 'desc')->get()
        );
    }

    public function getAllStatuses(): array
    {
        $statuses = TicketStatus::query()
            ->orderBy('sort_order', 'desc')
            ->get();

        return TicketStatusDto::fromCollection($statuses);
    }

    public function getStatusesWithCategories(): array
    {
        return TicketStatusDto::fromCollection(
            TicketStatus::query()
                ->categoriesOnly()
                ->get()
        );
    }

    public function getStatusPaginated(int $page, string $search): PaginatedListDto
    {
        $query = TicketStatus::query()
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: 10, page: $page),
            fn($tag) => TicketStatusesListDto::fromModel($tag)
        );
    }

    public function createStatus(TicketStatusCreateDto $dto): TicketStatus
    {
        $status = new TicketStatus([
            'name' => $dto->name,
            'slug' => SlugGenerator::generate($dto->name),
            'color' => $dto->color,
            'is_default' => $dto->is_default,
            'is_final' => $dto->is_final,
            'is_approval' => $dto->is_approval,
            'sort_order' => $dto->sort_order
        ]);

        $status->save();

        return $status;
    }

    public function updateStatus(int $statusId, TicketStatusCreateDto $dto): TicketStatus
    {
        $status = $this->getStatus($statusId);

        $status->update([
            'name' => $dto->name,
            'slug'        => SlugGenerator::generate($dto->name),
            'color' => $dto->color,
            'is_default' => $dto->is_default,
            'is_final' => $dto->is_final,
            'is_approval' => $dto->is_approval,
            'sort_order' => $dto->sort_order
        ]);

        return $status;
    }

    public function deleteStatus(int $statusId): void
    {
        $status = $this->getStatus($statusId);
        $status->delete();
    }
}
