<?php

namespace App\Services\Ticket;

use App\DTO\InfiniteScrollDto;
use App\DTO\Ticket\PlayerTicketCreateDto;
use App\DTO\Ticket\PlayerTicketListDto;
use App\DTO\Ticket\PlayerTicketStatusDto;
use App\DTO\Ticket\PlayerTicketUpdateDto;
use App\Enums\PlayerTicketStatusEnum;
use App\Models\ProductLog;
use App\Models\Ticket\PlayerTicket;
use App\Models\User\User;
use App\Services\Log\ProductLogService;
use App\Services\Ticket\PlayerTicketFileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPlayerService
{
    public function __construct(
        public readonly ProductLogService  $productLogService,
        public readonly PlayerTicketFileService $playerTicketFileService,
    ) {}

    public static function getStatusCatalog(): array
    {
        return array_map(
            fn (PlayerTicketStatusEnum $status) => PlayerTicketStatusDto::fromEnum($status),
            PlayerTicketStatusEnum::cases()
        );
    }

    public function getTicket(int $id): PlayerTicket
    {
        return PlayerTicket::query()->findOrFail($id);
    }

    public function getTicketsInfinite(?string $cursor, string $search, string $sort, array $filters, ?User $user = null): InfiniteScrollDto
    {
        $user = $user ?? Auth::user();

        $query = PlayerTicket::query()
            ->when($user, fn($q) => $q->ownedBy($user->id))
            ->sort($sort)
            ->search($search)
            ->filter($filters);

        $paginator = $query->cursorPaginate(perPage: 10, cursor: $cursor ? Cursor::fromEncoded($cursor) : null);

        return InfiniteScrollDto::fromCursorPaginator(
            $paginator,
            fn($ticket) => PlayerTicketListDto::fromModel($ticket)
        );
    }

    public function getTicketsModerationInfinity(?string $cursor, string $search, string $sort, array $filters, ?User $user = null): InfiniteScrollDto
    {
        $user = $user ?? Auth::user();

        $query = PlayerTicket::query()
            ->when($user, fn($q) => $q->forModerator($user))
            ->sort($sort)
            ->search($search)
            ->filter($filters);

        $paginator = $query->cursorPaginate(perPage: 10, cursor: $cursor ? Cursor::fromEncoded($cursor) : null);

        return InfiniteScrollDto::fromCursorPaginator(
            $paginator,
            fn($ticket) => PlayerTicketListDto::fromModel($ticket)
        );
    }

    public function createTicket(PlayerTicketCreateDto $dto, UploadedFile $screenFile): PlayerTicket
    {
        $is_valid_tg_id = $this->productLogService
            ->checkInProductLogsWithPlayerId($dto->player_id, 'tg_id', $dto->tg_id);

        $is_valid_sum = $this->productLogService->checkInProductLogsWithPlayerId($dto->player_id, 'dep_sum', $dto->sum);

        $ticket = PlayerTicket::create([
            'user_id'       => $dto->user_id,
            'status'        => 'On Approve',
            'player_id'     => $dto->player_id,
            'type'          => $dto->type,
            'tg_id'         => $dto->tg_id,
            'is_valid_tg_id'=> $is_valid_tg_id,
            'screen_url'    => '',
            'sum'           => $dto->sum,
            'is_valid_sum'  => $is_valid_sum,
        ]);

        // Загрузка скриншота из файла формы (URL более не поддерживается)
        try {
            $publicUrl = $this->playerTicketFileService->uploadScreenshot($screenFile, $ticket);
            $ticket->update(['screen_url' => $publicUrl]);
        } catch (\Throwable $e) {
            Log::warning('Failed to upload player ticket screenshot', [
                'ticket_id' => $ticket->id,
                'user_id' => $dto->user_id,
                'player_id' => $dto->player_id,
                'error' => $e->getMessage(),
            ]);
        }
        return $ticket;
    }

    public function updateTicket(PlayerTicket $ticket, PlayerTicketUpdateDto $dto): PlayerTicket
    {
        $ticket->update([
            'status' => $dto->status,
            'result' => $dto->result ?? null,
        ]);

        return $ticket;
    }
}
