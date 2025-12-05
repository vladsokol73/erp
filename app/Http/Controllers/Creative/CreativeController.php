<?php

namespace App\Http\Controllers\Creative;

use App\DTO\Creative\CreativeCreateDto;
use App\DTO\Creative\TagDto;
use App\Enums\PermissionEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\Creative\CreativeCreateRequest;
use App\Http\Requests\Creative\CreativeFileDeleteRequest;
use App\Http\Requests\Creative\CreativeFileUploadRequest;
use App\Http\Requests\FavoriteRequest;
use App\Http\Requests\ReactionRequest;
use App\Http\Responses\ApiResponse;
use App\Services\CommentService;
use App\Services\CountryService;
use App\Services\Creative\CreativeFileService;
use App\Services\Creative\CreativeService;
use App\Services\Creative\TagService;
use App\Services\FavoriteService;
use App\Services\LikeService;
use App\Services\User\UserService;
use App\Support\AccessRule;
use App\Support\PerPageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreativeController extends Controller
{
    public function __construct(
        public readonly CreativeService      $creativeService,
        public readonly CountryService       $countryService,
        public readonly UserService          $userService,
        public readonly TagService           $tagService,
        public readonly LikeService          $likeService,
        public readonly FavoriteService      $favoriteService,
        public readonly CommentService       $commentService,
        public readonly CreativeFileService  $creativeFileService,
        public readonly PerPageService       $perPageService,
    ) {}

    public function showLibrary(Request $request): Response
    {
        $page = $request->integer('page', 1);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);

        $perPage = $this->perPageService->resolve($request, 'creative_library', 16);

        return Inertia::render('Creative/Library', [
            'creatives' => fn() => $this->creativeService->getCreativePaginated($page, $search, $sort, $filter, $perPage),
            'creatives_countries' => fn() => $this->countryService->getCountriesWithCreatives(),
            'creatives_users' => fn() => $this->userService->getUsersWithCreatives(),
            'creatives_tags' => fn() => $this->tagService->getTagsWithCreatives(),

            'tags' => fn() => Guard::resolve([
                AccessRule::permission(PermissionEnum::CREATIVES_UPDATE->value, fn() => TagDto::fromCollection($this->tagService->getTags()))
            ], default: fn() => []),
        ]);
    }

    public function reactionsCreative(int $creativeId, ReactionRequest $request): JsonResponse
    {
        try {
            $type = $request->input('type');
            $creative = $this->creativeService->getCreative($creativeId);

            if ($type === 'like') {
                $this->likeService->like($creative);
            } else {
                $this->likeService->dislike($creative);
            }

            return ApiResponse::successMessage('Reaction successfully applied.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Creative not found.');
        }
    }

    public function favoritesCreative(int $creativeId, FavoriteRequest $request): JsonResponse
    {
        try {
            $type = $request->input('type');
            $creative = $this->creativeService->getCreative($creativeId);

            if ($type === 'favorite') {
                $this->favoriteService->add($creative);
            } else {
                $this->favoriteService->remove($creative);
            }

            return ApiResponse::successMessage('Favorite status successfully updated.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Creative not found.');
        }
    }

    public function commentsCreative(int $creativeId, CommentRequest $request): JsonResponse
    {
        try {
            $commentText = $request->input('comment');
            $creative = $this->creativeService->getCreative($creativeId);
            $comment = $this->commentService->addComment($creative, $commentText);

            return ApiResponse::success([
                'comment' => $comment,
            ], 'Comment successfully added.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Creative not found.');
        }
    }

    public function deleteCreative(int $creativeId): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_UPDATE->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        try {
            $this->creativeService->deleteCreative($creativeId);

            return ApiResponse::successMessage('Creative successfully deleted.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Creative not found.');
        }
    }

    public function updateTags(int $creativeId, Request $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_UPDATE->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        try {

            $tags = $request->input('tags');
            $this->creativeService->updateCreativeTags($creativeId, $tags);

            return ApiResponse::successMessage('Tags successfully updated.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Creative not found.');
        }
    }

    public function showNewCreative(): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_CREATE->value)) {
            return Inertia::render('Error/403');
        }

        return Inertia::render('Creative/NewCreative', [
            'countries' => $this->countryService->getCountries(),
            'tags' =>  TagDto::fromCollection($this->tagService->getTags()),
        ]);
    }



    public function showFavorites(Request $request): Response
    {
        $page = $request->integer('page', 1);
        $search = $request->string('search');
        $sort = $request->string('sort');
        $filter = $request->input('filter', []);
        $perPage = $request->integer('perPage', 16);

        return Inertia::render('Creative/Library', [
            'creatives' => fn() => $this->creativeService->getCreativePaginated($page, $search, $sort, $filter, $perPage, true),
            'creatives_countries' => fn() => $this->countryService->getCountriesWithCreatives(),
            'creatives_users' => fn() => $this->userService->getUsersWithCreatives(),
            'creatives_tags' => fn() => $this->tagService->getTagsWithCreatives(),

            'tags' => fn() => Guard::resolve([
                AccessRule::permission(PermissionEnum::CREATIVES_UPDATE->value, fn() => TagDto::fromCollection($this->tagService->getTags()))
            ], default: fn() => []),
        ]);
    }

    public function createCreative(CreativeCreateRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_CREATE->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $validated = $request->validated();
        $creativesCreateDTOs = [];
        $files = $validated['files'];

        if (is_string($files)) {
            $files = json_decode($files, true);
        }

        try {
            foreach ($files as $file) {
                $creativesCreateDTOs[] = new CreativeCreateDto(
                    code: $file['code'],
                    url: $file['url'],
                    type: $file['type'],
                    resolution: $file['resolution'] ?? null,
                    country_id: $validated['country_id'],
                    user_id: $request->user()->id,
                    tags: $validated['tags'] ?? [],
                );
            }

            $creatives = $this->creativeService->createCreative($creativesCreateDTOs);

            return ApiResponse::created([
                'creatives' => $creatives??[],
            ]);
        } catch (Exception $e) {
            return ApiResponse::serverError('Failed to create creative.');
        }
    }

    public function uploadFile(CreativeFileUploadRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_CREATE->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $type = $request->input('type', 'image');
        $file = $request->file('file');

        try {
            $creativeFile = $this->creativeFileService->uploadCreativeFile($file, $type);

            return ApiResponse::success($creativeFile, 'File uploaded successfully.');
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::validationMessage($e->getMessage());
        } catch (Exception $e) {
            return ApiResponse::serverError('File upload error: ' . $e->getMessage());
        }
    }

    public function deleteFile(CreativeFileDeleteRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CREATIVES_CREATE->value)) {
            return ApiResponse::forbidden('Permission denied.');
        }

        $url = $request->input('url');

        try {
            $deleted = $this->creativeFileService->deleteCreativeFile($url);

            return $deleted
                ? ApiResponse::successMessage('File successfully deleted.')
                : ApiResponse::notFound('File not found. Url is :' . $url);
        } catch (Exception) {
            return ApiResponse::serverError('File deletion error. Url is :' . $url);
        }
    }
}
