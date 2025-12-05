<?php

namespace App\Http\Controllers\Creative;

use App\DTO\Creative\TagCreateDto;
use App\DTO\Creative\TagListDto;
use App\Enums\PermissionEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Creative\TagService;
use App\Support\PerPageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function __construct(
        public readonly TagService      $tagService,
        public readonly PerPageService  $perPageService,
    )
    {
    }

    public function showTags(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_TAGS->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        $perPage = $this->perPageService->resolve($request, 'creative_tags', 10);

        return Inertia::render('Creative/Tags', [
            'tags' => fn() => $this->tagService->getTagPaginated($page, $search, $perPage),
        ]);
    }

    public function createTag(TagRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_TAGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();
        $tagCreateDto = new TagCreateDto(
            name: $validated['name'],
            style: $validated['style'],
        );

        try {
            $tag = $this->tagService->createTag($tagCreateDto);

            return ApiResponse::created([
                'tag' => TagListDto::FromModel($tag),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create tag.');
        }
    }

    public function updateTag(int $id, TagRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_TAGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();

        $tagUpdateDto = new TagCreateDto(
            name: $validated['name'],
            style: $validated['style'],
        );

        try {
            $tag = $this->tagService->updateTag($id, $tagUpdateDto);

            return ApiResponse::success([
                'tag' => TagListDto::fromModel($tag),
            ]);
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Tag with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError('Failed to update tag.');
        }
    }

    public function deleteTag(int $id): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_TAGS->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        try {
            $this->tagService->deleteTag($id);

            return ApiResponse::success(message: "Tag deleted successfully.");
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound("Tag with ID {$id} not found.");
        } catch (Exception) {
            return ApiResponse::serverError("Failed to delete tag.");
        }
    }
}
