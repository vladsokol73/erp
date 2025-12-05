<?php

namespace App\DTO\Ticket;
use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\DTO\CommentDto;
use App\DTO\User\UserDto;
use App\Models\Ticket\Ticket;
use App\Services\Ticket\TicketService;
use App\Services\Ticket\TicketStatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int                      $id,
        public readonly string                   $ticket_number,
        #[LiteralTypeScriptType('TicketTopicListDto')]
        public readonly TicketTopicListDto      $topic,
        #[LiteralTypeScriptType('TicketStatusDto')]
        public readonly TicketStatusDto          $status,
        #[LiteralTypeScriptType('CommentDto[]')]
        public readonly array                    $comments = [],
        #[LiteralTypeScriptType('TicketResponsibleUserDto[]')]
        public readonly array $approval = [],
        #[LiteralTypeScriptType('TicketResponsibleUserDto[]')]
        public readonly array $responsible = [],
        #[LiteralTypeScriptType('TicketFieldValuesListDto[]')]
        public readonly array $fieldValues = [],
        #[LiteralTypeScriptType('App.DTO.User.UserDto')]
        public readonly UserDto $user,
        public readonly string $priority,
        public readonly string $created_at,
        #[LiteralTypeScriptType('TicketStatusDto[]')]
        public readonly array $available_statuses = [],
        public readonly string|null $result = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'topic' => $this->topic->toArray(),
            'status' => $this->status->toArray(),
            'comments' => $this->comments,
            'approval' => $this->approval,
            'responsible' => $this->responsible,
            'field_values_list' => $this->fieldValues,
            'user' => $this->user,
            'priority' => $this->priority,
            'result' => $this->result,
            'created_at' => $this->created_at,
            'available_statuses' => array_map(fn($s) => $s->toArray(), $this->available_statuses),
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Ticket)) {
            throw new \InvalidArgumentException('Expected Ticket instance');
        }

        $ticketService = app(TicketService::class);
        $topic = $model->topic()->withTrashed()->first();
        $category = $topic?->category()->with('statuses')->first();
        $categoryStatuses = $category?->statuses ?? collect();

        return new self(
            id: $model->id,
            ticket_number: $model->ticket_number,
            topic: TicketTopicListDto::fromModel($topic),
            status: TicketStatusDto::fromModel($model->status()->withTrashed()->first()),
            comments: CommentDto::fromCollection($model->comments),
            approval: $ticketService->getApproval($model->topic),
            responsible: $ticketService->getResponsible($model->topic),
            fieldValues: TicketFieldValuesListDto::fromCollection($model->fieldValues()->get()),
            user: UserDto::fromModel($model->user()->withTrashed()->first()),
            priority: $model->priority,
            created_at: $model->created_at,
            available_statuses: $categoryStatuses
                ->map(fn($status) => TicketStatusDto::fromModel($status))
                ->all(),
            result: $model->result,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
