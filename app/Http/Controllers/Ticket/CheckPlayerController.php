<?php

namespace App\Http\Controllers\Ticket;

use App\DTO\Ticket\PlayerTicketCreateDto;
use App\DTO\Ticket\PlayerTicketDto;
use App\DTO\Ticket\PlayerTicketListDto;
use App\DTO\Ticket\PlayerTicketUpdateDto;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\Ticket\PlayerTicketCreateRequest;
use App\Http\Requests\Ticket\PlayerTicketUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Ticket\PlayerTicket;
use App\Services\CommentService;
use App\Services\Log\ProductLogService;
use App\Services\NotificationService;
use App\Services\Ticket\CheckPlayerService;
use App\Services\User\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckPlayerController
{
    public function __construct(
        public readonly CheckPlayerService  $checkPlayerService,
        public readonly CommentService      $commentService,
        public readonly NotificationService $notificationService,
        public readonly ProductLogService   $productLogService,
        public readonly UserService         $userService,
    ) {}
    public function showMy(Request $request):Response
    {
        if (!Guard::role()->hasRole(RoleEnum::OPERATOR->value)
            && !Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_SHOW->value)) {
            return Inertia::render('Error/403');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return Inertia::render('Tickets/CheckPlayer/My', [
            'tickets' => $this->checkPlayerService->getTicketsInfinite($cursor, $search, $sort, $filter),
            'statusCatalog' => $this->checkPlayerService->getStatusCatalog(),
        ]);
    }

    public function getMyMore(Request $request): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::OPERATOR->value)
            && !Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_SHOW->value)) {
            return ApiResponse::forbidden('You do not have permission to see this tickets.');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return ApiResponse::success([
            'tickets' => $this->checkPlayerService->getTicketsInfinite($cursor, $search, $sort, $filter),
        ]);
    }

    public function commentsPlayer(int $ticketId, CommentRequest $request): JsonResponse
    {
        try {
            $commentText = $request->input('comment');
            $ticket = $this->checkPlayerService->getTicket($ticketId);

            if (!Guard::owns($ticket)
                && !Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_MODERATION->value)) {
                return ApiResponse::forbidden('You do not have permission to comment this ticket.');
            }

            $comment = $this->commentService->addComment($ticket, $commentText);

            $this->notificationService->notifyCommentPlayerTicket($request->user()->id ,$ticket);

            return ApiResponse::success([
                'comment' => $comment,
            ], 'Comment successfully added.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }
    }

    public function createTicket(PlayerTicketCreateRequest $request): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::OPERATOR->value)
            && !Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_SHOW->value)) {
            return ApiResponse::forbidden('You do not have permission to create ticket.');
        }

        $validated = $request->validated();

        $ticketCreateDto = new PlayerTicketCreateDto(
            user_id: $request->user()->id,
            player_id: $validated['player_id'],
            type: $validated['type'],
            tg_id: $validated['tg_id'],
            sum: $validated['sum'],
        );

        try {
            $ticket = $this->checkPlayerService->createTicket($ticketCreateDto, $request->file('screen'));

            return ApiResponse::created([
                'ticket' => PlayerTicketListDto::fromModel($ticket),
            ]);
        } catch (Exception $e) {
            return ApiResponse::serverError('Failed to create ticket. \n' . $e->getMessage());
        }
    }

    public function showModeration(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_MODERATION->value)) {
            return Inertia::render('Error/403');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return Inertia::render('Tickets/CheckPlayer/Moderation', [
            'tickets' => $this->checkPlayerService->getTicketsModerationInfinity($cursor, $search, $sort, $filter),
            'statusCatalog' => $this->checkPlayerService->getStatusCatalog(),
            'operators' => $this->userService->getOperators(),
        ]);
    }

    public function getModerationMore(Request $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_MODERATION->value)) {
            return ApiResponse::forbidden('You do not have permission to get tickets.');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return ApiResponse::success([
            'tickets' => $this->checkPlayerService->getTicketsModerationInfinity($cursor, $search, $sort, $filter),
        ]);
    }

    public function updateTicket(int $ticketId, PlayerTicketUpdateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_MODERATION->value)) {
            return ApiResponse::forbidden('You do not have permission to edit this ticket.');
        }

        try {
            $validated = $request->validated();
            $ticket = $this->checkPlayerService->getTicket($ticketId);

            $ticketUpdateDto = new PlayerTicketUpdateDto(
                status: $validated['status'],
                result: $validated['result'] ?? null,
            );

            $ticket = $this->checkPlayerService->updateTicket($ticket, $ticketUpdateDto);

            return ApiResponse::success(
                [
                    "ticket" => PlayerTicketListDto::fromModel($ticket)
                ],
                'Ticket successfully updated.'
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }
    }
}
