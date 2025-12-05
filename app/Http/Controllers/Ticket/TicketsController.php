<?php

namespace App\Http\Controllers\Ticket;

use App\DTO\Ticket\TicketCreateDto;
use App\DTO\Ticket\TicketDto;
use App\DTO\Ticket\TicketListDto;
use App\DTO\Ticket\TicketUpdateDto;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\Ticket\TicketCreateRequest;
use App\Http\Requests\Ticket\TicketUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Services\CommentService;
use App\Services\CountryService;
use App\Services\NotificationService;
use App\Services\ProjectService;
use App\Services\Ticket\TicketCategoryService;
use App\Services\Ticket\TicketFormFieldService;
use App\Services\Ticket\TicketService;
use App\Services\Ticket\TicketStatusService;
use App\Services\Ticket\TicketTopicService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketsController extends Controller
{
    public function __construct(
        public readonly TicketService          $ticketService,
        public readonly TicketCategoryService  $ticketCategoryService,
        public readonly TicketTopicService     $ticketTopicService,
        public readonly TicketFormFieldService $ticketFormFieldService,
        public readonly TicketStatusService    $ticketStatusService,
        public readonly CountryService         $countryService,
        public readonly CommentService         $commentService,
        public readonly NotificationService    $notificationService,
        public readonly ProjectService         $projectService,
    ) {}

    public function showCreate(): Response
    {
        return Inertia::render(
            'Tickets/Create', [
                'categories' => $this->ticketCategoryService->getCategories(),
                'topics' => $this->ticketTopicService->getAllTopics(),
                'countries' => $this->countryService->getCountries(),
                'projects' => $this->projectService->getProjects()
            ]
        );
    }
    public function createTicket(TicketCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Создаём DTO с новой структурой
        $ticketCreateDto = new TicketCreateDto(
            user_id: $request->user()->id,
            category_id: $validated['category_id'],
            topic_id: $validated['topic_id'],
            priority: $validated['priority'],
            fields: $validated['fields'] ?? []
        );

        try {
            $ticket = $this->ticketService->createTicket($ticketCreateDto);

            return ApiResponse::created([
                'ticket' => TicketDto::fromModel($ticket),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create ticket. ');
        }
    }


    public function showMy(Request $request): Response
    {
        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return Inertia::render('Tickets/My', [
            'tickets' => $this->ticketService->getMyTicketsInfinite($cursor, $search, $sort, $filter),
            'countries' => $this->countryService->getCountries(),
            'projects' => $this->projectService->getProjects(),
            'statuses' => $this->ticketStatusService->getStatusesWithCategories(),
            'topics' => $this->ticketTopicService->getAllTopics(),
            'categories' => $this->ticketCategoryService->getCategories()
        ]);
    }

    public function getMyMore(Request $request): JsonResponse
    {
        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return ApiResponse::success([
            'tickets' => $this->ticketService->getMyTicketsInfinite($cursor, $search, $sort, $filter),
        ]);
    }

    public function showModeration(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_MODERATE->value)) {
            return Inertia::render('Error/403');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return Inertia::render('Tickets/Moderation', [
            'tickets' => $this->ticketService->getModerationTicketsInfinite($cursor, $search, $sort, $filter),
            'countries' => $this->countryService->getCountries(),
            'projects' => $this->projectService->getProjects(),
            'statuses' => $this->ticketStatusService->getStatusesWithCategories(),
            'topics' => $this->ticketTopicService->getAllTopics(),
            'categories' => $this->ticketCategoryService->getCategories(),
        ]);
    }

    public function getModerationMore(Request $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_MODERATE->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return ApiResponse::success([
            'tickets' => $this->ticketService->getModerationTicketsInfinite($cursor, $search, $sort, $filter),
        ]);
    }

    public function showAll(Request $request): Response
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return Inertia::render('Error/403');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return Inertia::render('Tickets/All', [
            'tickets' => $this->ticketService->getAllTicketsInfinite($cursor, $search, $sort, $filter),
            'countries' => $this->countryService->getCountries(),
            'projects' => $this->projectService->getProjects(),
            'statuses' => $this->ticketStatusService->getStatusesWithCategories(),
            'topics' => $this->ticketTopicService->getAllTopics(),
            'categories' => $this->ticketCategoryService->getCategories(),
        ]);
    }

    public function getAllMore(Request $request): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $cursor = $request->integer('cursor', null);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        return ApiResponse::success([
            'tickets' => $this->ticketService->getAllTicketsInfinite($cursor, $search, $sort, $filter),
        ]);
    }

    public function commentsTicket(int $ticketId, CommentRequest $request): JsonResponse
    {
        try {
            $commentText = $request->input('comment');
            $ticket = $this->ticketService->getTicket($ticketId);
            $comment = $this->commentService->addComment($ticket, $commentText);

            $this->notificationService->notifyCommentTicket($request->user()->id ,$ticket);

            return ApiResponse::success([
                'comment' => $comment,
            ], 'Comment successfully added.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }
    }

    public function approveTicket(int $ticketId): JsonResponse
    {
        try {
            $ticket = $this->ticketService->getTicket($ticketId);

            if (!Guard::owns($ticket)) {
                return ApiResponse::forbidden('You do not have permission to edit this ticket.');
            }

            $ticket = $this->ticketService->approveTicket($ticketId);

            return ApiResponse::success([
                'ticket' => TicketListDto::fromModel($ticket),
            ], 'Ticket successfully send to approve.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }
    }

    public function updateOwnerTicket(int $ticketId, TicketUpdateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $ticket = $this->ticketService->getTicket($ticketId);

            if (!Guard::owns($ticket) && !$ticket->status->is_default) {
                return ApiResponse::forbidden('You do not have permission to edit this ticket.');
            }

            $ticketUpdateDto = new TicketUpdateDto(
                status_id: $ticket->status_id,
                user_id: $ticket->user_id,
                priority: $validated['priority'] ?? null,
                result: $ticket->result ?? null,
                fields: $validated['fields'] ?? []
            );

            $ticket = $this->ticketService->updateOwnerTicket($ticket, $ticketUpdateDto);

            return ApiResponse::success(
                [
                    "ticket" => TicketListDto::fromModel($ticket)
                ],
                'Ticket successfully updated.'
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }
    }

    public function updateModeratorTicket(int $ticketId, TicketUpdateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_MODERATE->value)) {
            return ApiResponse::forbidden('You do not have permission to edit this ticket.');
        }

        try {
            $validated = $request->validated();
            $ticket = $this->ticketService->getTicket($ticketId);

            $ticketUpdateDto = new TicketUpdateDto(
                status_id: $validated['status_id'],
                result: $validated['result'] ?? null,
            );

            $ticket = $this->ticketService->updateModeratorTicket($ticket, $ticketUpdateDto);

            return ApiResponse::success(
                [
                    "ticket" => TicketListDto::fromModel($ticket)
                ],
                'Ticket successfully updated.'
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }

    }

    public function updateAdminTicket(int $ticketId, TicketUpdateRequest $request): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $validated = $request->validated();
            $ticket = $this->ticketService->getTicket($ticketId);

            $ticketUpdateDto = new TicketUpdateDto(
                status_id: $validated['status_id'],
                user_id: $ticket->user_id,
                priority: $validated['priority'] ?? null,
                result: $validated['result'] ?? null,
                fields: $validated['fields'] ?? []
            );

            $ticket = $this->ticketService->updateAdminTicket($ticket, $ticketUpdateDto);

            return ApiResponse::success(
                [
                    "ticket" => TicketListDto::fromModel($ticket)
                ],
                'Ticket successfully updated.'
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }

    }

    public function deleteTicket(int $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->getTicket($ticketId);

        $isAdmin = Guard::role()->hasRole(RoleEnum::ADMIN->value);
        $isOwnerWithDefaultStatus = Guard::owns($ticket) && $ticket->status->is_default;

        if (!($isAdmin || $isOwnerWithDefaultStatus)) {
            return ApiResponse::forbidden('You do not have permission to delete this ticket.');
        }

        try {
            $this->ticketService->deleteTicket($ticket);

            return ApiResponse::success("Ticket successfully deleted.");
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Ticket not found.');
        }
    }
}
