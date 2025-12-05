<?php

namespace App\Services\Ticket;

use App\DTO\PaginatedListDto;
use App\DTO\Ticket\TicketTopicCreateDto;
use App\DTO\Ticket\TicketTopicDto;
use App\DTO\Ticket\TicketTopicListDto;
use App\Models\Ticket\TicketTopic;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Model;

class TicketTopicService
{
    public function __construct(
        protected TicketResponsibleUserService $ticketResponsibleUserService
    ) {}

    public function getTopic(int $topicId): TicketTopic
    {
        return TicketTopic::query()->findOrFail($topicId);
    }

    public function getTopics( Model $model ): array
    {
        if (!($model instanceof TicketTopic)) {
            throw new \InvalidArgumentException('Expected TicketTopic type model');
        }

        return TicketTopicDto::fromCollection(
            $model->statuses()->orderBy('sort_order', 'desc')->get()
        );
    }

    public function getAllTopics(): array
    {
        $topics = TicketTopic::query()
            ->orderBy('sort_order', 'desc')
            ->get();

        return TicketTopicDto::fromCollection($topics);
    }

    public function getTopicPaginated(int $page, string $search): PaginatedListDto
    {
        $query = TicketTopic::query()
            ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: 10, page: $page),
            fn($topic) => TicketTopicListDto::fromModel($topic)
        );
    }

    public function createTopic(TicketTopicCreateDto $dto): TicketTopic
    {
        // Создание основной сущности
        $topic = TicketTopic::create([
            'name'        => $dto->name,
            'slug'        => SlugGenerator::generate($dto->name),
            'description' => $dto->description,
            'category_id' => $dto->category_id,
            'sort_order'  => $dto->sort_order,
            'is_active'   => $dto->is_active,
        ]);

        // Присвоение approval[]
        $this->ticketResponsibleUserService->syncResponsiblesForApproval($topic, $dto->approval);

        // Присвоение responsible[]
        $this->ticketResponsibleUserService->syncResponsiblesForTopic($topic, $dto->responsible);

        // Присвоение formFields с учётом sort_order
        $pivotData = collect($dto->fields)
            ->mapWithKeys(fn($field, $index) => [
                $field->id => ['sort_order' => $index],
            ])
            ->all();

        $topic->formFields()->sync($pivotData);

        return $topic;
    }


    public function updateTopic(int $topicId, TicketTopicCreateDto $dto): TicketTopic
    {
        $topic = $this->getTopic($topicId);

        // Обновление основной информации
        $topic->update([
            'name'        => $dto->name,
            'slug'        => SlugGenerator::generate($dto->name),
            'description' => $dto->description,
            'category_id' => $dto->category_id,
            'sort_order'  => $dto->sort_order,
            'is_active'   => $dto->is_active,
        ]);

        // Синхронизация массива approval[]
        $this->ticketResponsibleUserService->syncResponsiblesForApproval($topic, $dto->approval);

        // Синхронизация массива responsible[]
        $this->ticketResponsibleUserService->syncResponsiblesForTopic($topic, $dto->responsible);

        // Сформируем массив вида [field_id => ['sort_order' => index]]
        $pivotData = collect($dto->fields)
            ->mapWithKeys(fn($field, $index) => [
                $field->id => ['sort_order' => $index],
            ])
            ->all();

        $topic->formFields()->sync($pivotData);

        return $topic;
    }

    public function deleteTopic(int $topicId): void
    {
        $topic = $this->getTopic($topicId);
        $topic->delete();
    }
}
