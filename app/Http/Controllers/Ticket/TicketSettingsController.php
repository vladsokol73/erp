<?php

namespace App\Http\Controllers\Ticket;

use App\DTO\Ticket\TicketCategoriesListDto;
use App\DTO\Ticket\TicketCategoryCreateDto;
use App\DTO\Ticket\TicketFormFieldCreateDto;
use App\DTO\Ticket\TicketFormFieldDto;
use App\DTO\Ticket\TicketFormFieldListDto;
use App\DTO\Ticket\TicketResponsibleUserDto;
use App\DTO\Ticket\TicketStatusCreateDto;
use App\DTO\Ticket\TicketStatusesListDto;
use App\DTO\Ticket\TicketTopicCreateDto;
use App\DTO\Ticket\TicketTopicListDto;
use App\Enums\PermissionEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\TicketCategoryCreateRequest;
use App\Http\Requests\Ticket\TicketFormFieldCreateRequest;
use App\Http\Requests\Ticket\TicketStatusCreateRequest;
use App\Http\Requests\Ticket\TicketTopicCreateRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Ticket\TicketCategoryService;
use App\Services\Ticket\TicketFormFieldService;
use App\Services\Ticket\TicketStatusService;
use App\Services\Ticket\TicketTopicService;
use App\Services\User\PermissionService;
use App\Services\User\RoleService;
use App\Services\User\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketSettingsController extends Controller
{
    public function __construct(
        public readonly TicketStatusService $ticketStatusService,
        public readonly TicketCategoryService $ticketCategoryService,
        public readonly TicketTopicService $ticketTopicService,
        public readonly UserService $userService,
        public readonly RoleService $roleService,
        public readonly PermissionService $permissionService,
        public readonly TicketFormFieldService $ticketFormFieldService
    )
    {
    }

    public function showSettingsCategory(Request $request): Response
    {

        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        return Inertia::render('Tickets/Settings/Categories', [
            'ticketsCategories' => fn() => $this->ticketCategoryService->getCategoryPaginated($page, $search),
            'ticketsStatuses' => fn() => $this->ticketStatusService->getAllStatuses(),
        ]);
    }

    public function updateCategory(int $id, TicketCategoryCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();

        $categoryUpdateDto = new TicketCategoryCreateDto(
            name: $validated['name'],
            is_active: (bool) $validated['is_active'],
            description: $validated['description'],
            sort_order: $validated['sort_order'],
            statuses: $validated['statuses'],
        );

        try {
            $category = $this->ticketCategoryService->updateCategory($id, $categoryUpdateDto);

            return ApiResponse::success([
                'category' => TicketCategoriesListDto::fromModel($category),
            ]);
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Status with ID {$id} not found.");
        } catch (Exception $e) {
            return ApiResponse::serverError('Failed to update status.'.$e);
        }
    }

    public function createCategory(TicketCategoryCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();

        $categoryCreateDto = new TicketCategoryCreateDto(
            name: $validated['name'],
            is_active: (bool) $validated['is_active'],
            description: $validated['description'],
            sort_order: $validated['sort_order'],
            statuses: $validated['statuses'],
        );

        try {
            $category = $this->ticketCategoryService->createCategory($categoryCreateDto);

            return ApiResponse::created([
                'status' => TicketCategoriesListDto::FromModel($category),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create status.');
        }
    }

    public function deleteCategory(int $id): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        try {
            $this->ticketCategoryService->deleteCategory($id);

            return ApiResponse::success(message: "Category deleted successfully.");
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Category with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError("Failed to delete category.");
        }
    }

    public function showSettingsStatuses(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        return Inertia::render('Tickets/Settings/Statuses', [
            'ticketsStatuses' => fn() => $this->ticketStatusService->getStatusPaginated($page, $search),
        ]);
    }

    public function createStatuses(TicketStatusCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();
        $statusCreateDto = new TicketStatusCreateDto(
            name: $validated['name'],
            color: $validated['color'],
            is_default: (bool) $validated['is_default'],
            is_final: (bool) $validated['is_final'],
            is_approval: (bool) $validated['is_approval'],
            sort_order: $validated['sort_order'],
        );

        try {
            $status = $this->ticketStatusService->createStatus($statusCreateDto);

            return ApiResponse::created([
                'status' => TicketStatusesListDto::FromModel($status),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create status.');
        }
    }

    public function updateStatus(int $id, TicketStatusCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();
        $statusUpdateDto = new TicketStatusCreateDto(
            name: $validated['name'],
            color: $validated['color'],
            is_default: (bool) $validated['is_default'],
            is_final: (bool) $validated['is_final'],
            is_approval: (bool) $validated['is_approval'],
            sort_order: $validated['sort_order'],
        );

        try {
            $status = $this->ticketStatusService->updateStatus($id, $statusUpdateDto);

            return ApiResponse::success([
                'status' => TicketStatusesListDto::fromModel($status),
            ]);
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Status with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError('Failed to update status.');
        }
    }

    public function deleteStatus(int $id): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        try {
            $this->ticketStatusService->deleteStatus($id);

            return ApiResponse::success(message: "Status deleted successfully.");
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Status with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError("Failed to delete status.");
        }
    }

    public function showSettingsTopic(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        return Inertia::render('Tickets/Settings/Topics', [
            'ticketsTopics' => fn() => $this->ticketTopicService->getTopicPaginated($page, $search),
            'topicCategories' => fn() => $this->ticketCategoryService->getCategories(),
            'allUsers' => fn() => $this->userService->getUsers(),
            'allRoles' => fn() => $this->roleService->getRoles(),
            'allPermissions' => fn() => $this->permissionService->getPermissions(),
            'allFormFields' => fn() => $this->ticketFormFieldService->getFormFields(),
        ]);
    }

    public function createTopic(TicketTopicCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();

        $topicCreateDto = new TicketTopicCreateDto(
            name: $validated['name'],
            description: $validated['description'] ?? null,
            category_id: $validated['category_id'],
            sort_order: $validated['sort_order'] ?? 0,
            is_active: (bool) $validated['is_active'],

            approval: collect($validated['approval'] ?? [])
                ->map(fn(array $item) => TicketResponsibleUserDto::fromArray($item))
                ->all(),

            responsible: collect($validated['responsible'] ?? [])
                ->map(fn(array $item) => TicketResponsibleUserDto::fromArray($item))
                ->all(),

            fields: collect($validated['fields'] ?? [])
                ->map(fn(array $item) => TicketFormFieldDto::fromArray($item))
                ->all()
        );

        try {
            $topic = $this->ticketTopicService->createTopic($topicCreateDto);
            //Догружаем связанные данные
            $topic->load(['category', 'responsibleUsers', 'formFields']);

            return ApiResponse::created([
                'topic' => TicketTopicListDto::fromModel($topic),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create topic.');
        }
    }

    public function updateTopic(int $id, TicketTopicCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();

        $topicUpdateDto = new TicketTopicCreateDto(
            name: $validated['name'],
            description: $validated['description'] ?? null,
            category_id: $validated['category_id'],
            sort_order: $validated['sort_order'] ?? 0,
            is_active: (bool) $validated['is_active'],

            approval: collect($validated['approval'] ?? [])
                ->map(fn(array $item) => TicketResponsibleUserDto::fromArray($item))
                ->all(),

            responsible: collect($validated['responsible'] ?? [])
                ->map(fn(array $item) => TicketResponsibleUserDto::fromArray($item))
                ->all(),
            fields: collect($validated['fields'] ?? [])
                ->map(fn(array $item) => TicketFormFieldDto::fromArray($item))
                ->all()
        );

        try {
            $topic = $this->ticketTopicService->updateTopic($id, $topicUpdateDto);

            return ApiResponse::success([
                'topic' => TicketTopicListDto::fromModel($topic),
            ]);
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Topic with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError('Failed to update topic.');
        }
    }

    public function deleteTopic(int $id): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }
        $this->ticketTopicService->deleteTopic($id);
        try {


            return ApiResponse::success(message: "Topic deleted successfully.");
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Topic with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError("Failed to delete topic.");
        }
    }



    public function showSettingsFields(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        return Inertia::render('Tickets/Settings/Fields', [
            'ticketsFormFields' => fn() => $this->ticketFormFieldService->getFormFieldsPaginated($page, $search),
        ]);
    }

    public function updateFields(int $id, TicketFormFieldCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();

        $topicFieldUpdateDto = new TicketFormFieldCreateDto(
            name: $validated['name'],
            label: $validated['label'],
            type: $validated['type'],
            validation_rules: $validated['validation_rules'],
            options: $validated['options'],
            is_required: (bool) $validated['is_required']
        );

        try {
            $topic = $this->ticketFormFieldService->updateFormField($id, $topicFieldUpdateDto);

            return ApiResponse::success([
                'formField' => TicketFormFieldListDto::fromModel($topic),
            ]);
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Topic with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError('Failed to update topic.');
        }
    }

    public function createFields(TicketFormFieldCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();

        $fieldCreateDto = new TicketFormFieldCreateDto(
            name:             $validated['name'],
            label:            $validated['label'],
            type:             $validated['type'],
            validation_rules: $validated['validation_rules'] ?? [],
            options:          $validated['options']          ?? [],
            is_required:      (bool) $validated['is_required'],
        );

        try {
            $field = $this->ticketFormFieldService->createFormField($fieldCreateDto);

            return ApiResponse::created([
                'formField' => TicketFormFieldListDto::fromModel($field),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create form field.');
        }
    }

    public function deleteFields(int $fieldId): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::TICKETS_SETTINGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        try {
            $this->ticketFormFieldService->deleteFormField($fieldId);

            return ApiResponse::success(message: "Form field deleted successfully.");
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Form field with ID {$fieldId} not found.");
        } catch (\Exception) {
            return ApiResponse::serverError("Failed to delete form field.");
        }
    }
}
