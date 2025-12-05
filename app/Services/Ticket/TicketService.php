<?php

namespace App\Services\Ticket;

use App\DTO\InfiniteScrollDto;
use App\DTO\PaginatedListDto;
use App\DTO\Ticket\TicketCreateDto;
use App\DTO\Ticket\TicketListAllDto;
use App\DTO\Ticket\TicketListDto;
use App\DTO\Ticket\TicketLogCreateDto;
use App\DTO\Ticket\TicketResponsibleUserDto;
use App\DTO\Ticket\TicketUpdateDto;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketTopic;
use App\Models\User\User;
use App\Services\NotificationService;
use App\Services\User\UserService;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Facades\Auth;

class TicketService
{
    public function __construct(
        protected TicketFileService        $fileService,
        protected TicketStatusService      $statusService,
        protected TicketLogService         $logService,
        protected NotificationService      $notificationService,
        protected TicketFieldValuesService $fieldValuesService,
        protected UserService              $userService,
    )
    {
    }

    public function getTicket(int $id): Ticket
    {
        return Ticket::query()->findOrFail($id);
    }

    public function getMyTicketsInfinite(?string $cursor, string $search, string $sort, array $filters, ?User $user = null): InfiniteScrollDto
    {
        $user = $user ?? Auth::user();

        $query = Ticket::query()
            ->ownedBy(userId: $user->id)
            ->sort($sort)
            ->search($search)
            ->filter($filters);

        $paginator = $query
            ->cursorPaginate(perPage: 10, cursor: $cursor ? Cursor::fromEncoded($cursor) : null);

        return InfiniteScrollDto::fromCursorPaginator(
            $paginator,
            fn($ticket) => TicketListDto::fromModel($ticket)
        );
    }

    public function getModerationTicketsInfinite(?string $cursor, string $search, string $sort, array $filters, ?User $user = null): InfiniteScrollDto
    {
        $user = $user ?? Auth::user();

        $query = Ticket::query()
            ->moderatedBy(user: $user)
            ->sort($sort)
            ->search($search)
            ->filter($filters);

        $paginator = $query
            ->cursorPaginate(perPage: 10, cursor: $cursor ? Cursor::fromEncoded($cursor) : null);

        return InfiniteScrollDto::fromCursorPaginator(
            $paginator,
            fn($ticket) => TicketListDto::fromModel($ticket)
        );
    }

    public function getAllTicketsInfinite(?string $cursor, string $search, string $sort, array $filters, ?User $user = null): InfiniteScrollDto
    {
        $user = $user ?? Auth::user();

        $query = Ticket::query()
            ->sort($sort)
            ->search($search)
            ->filter($filters);

        $paginator = $query
            ->cursorPaginate(perPage: 10, cursor: $cursor ? Cursor::fromEncoded($cursor) : null);

        return InfiniteScrollDto::fromCursorPaginator(
            $paginator,
            fn($ticket) => TicketListAllDto::fromModel($ticket)
        );
    }

    public function getApproval(TicketTopic $ticketTopic): array
    {
        return TicketResponsibleUserDto::fromCollection($ticketTopic->approvalUsers()->get());
    }

    public function getResponsible(TicketTopic $ticketTopic): array
    {
        return TicketResponsibleUserDto::fromCollection($ticketTopic->responsibleUsers()->get());
    }

    public function createTicket(TicketCreateDto $dto): Ticket
    {
        $status = $this->statusService->getDefaultStatus($dto->category_id);

        $ticket = new Ticket([
            'topic_id' => $dto->topic_id,
            'user_id' => $dto->user_id,
            'status_id' => $status->id,
            'priority' => $dto->priority,
        ]);

        $ticket->save();

        $this->fieldValuesService->updateTicketFieldValues($ticket, $dto->fields);

        return $ticket;
    }

    public function approveTicket(int $ticketId, User|null $user = null): Ticket
    {
        $user = $user ?? Auth::user();

        $ticket = $this->getTicket($ticketId);

        if (!$ticket->status->is_default) {
            throw new \InvalidArgumentException('Ticket is already approved');
        }

        $new_status = $ticket->statuses()->where('is_approval', true)->first();

        $ticket->status_id = $new_status->id;
        $ticket->save();

        $this->logService->createLog(new TicketLogCreateDto(
            ticket_id: $ticketId,
            user_id: $user->id,
            action: 'Status To Approve',
            old_values: $ticket->status->name,
            new_values: $new_status->name,
        ));

        $this->notificationService->notifyTicketApproval($ticket);

        return $ticket;
    }

    public function updateOwnerTicket(Ticket $ticket, TicketUpdateDto $dto): Ticket
    {
        if (!$ticket->status->is_default) {
            throw new \InvalidArgumentException('Ticket is already approved');
        }

        $this->logService->createLog(new TicketLogCreateDto(
            ticket_id: $ticket->id,
            user_id: $dto->user_id,
            action: 'Update Ticket By Owner',
            old_values: "Old values",
            new_values: json_encode($dto->fields)
        ));

        return $this->updateTicket($ticket, $dto);
    }

    public function updateModeratorTicket(Ticket $ticket, TicketUpdateDto $dto, User|null $user = null): Ticket
    {
        $user = $user ?? Auth::user();

        $this->logService->createLog(new TicketLogCreateDto(
            ticket_id: $ticket->id,
            user_id: $user->id,
            action: 'Update Ticket By Moderator',
            old_values: "Status: " . $ticket->status_id . " Result: " . $ticket->result,
            new_values: "Status: " . $dto->status_id . " Result: " . $dto->result,
        ));

        $oldStatus = $ticket->status;
        $newStatus = $this->statusService->getStatus($dto->status_id);

        $this->notificationService->handleTicketStatusChange(
            ticket: $ticket,
            oldStatus: $oldStatus,
            newStatus: $newStatus,
            user: $this->userService->getUserById($ticket->user_id)
        );

        return $this->updateTicket($ticket, $dto);
    }

    public function updateAdminTicket(Ticket $ticket, TicketUpdateDto $dto): Ticket
    {
        $this->logService->createLog(new TicketLogCreateDto(
            ticket_id: $ticket->id,
            user_id: $dto->user_id,
            action: 'Update Ticket By Admin',
            old_values: "Old values",
            new_values: "New values",
        ));

        return $this->updateTicket($ticket, $dto);
    }

    private function updateTicket(Ticket $ticket, TicketUpdateDto $dto): Ticket
    {
        $ticket->update([
            'status_id' => $dto->status_id,
            'priority' => $dto->priority? : $ticket->priority,
            'result' => $dto->result,
        ]);

        if (!empty($dto->fields)) {
            $this->fieldValuesService->updateTicketFieldValues($ticket, $dto->fields);
        }

        return $ticket;
    }

    public function deleteTicket(Ticket $ticket): void
    {
        $ticket->delete();
    }
}
