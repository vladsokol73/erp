<?php

namespace App\Http\Controllers;

use App\DTO\Shorter\DomainCreateDto;
use App\DTO\Shorter\UrlCreateDto;
use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Requests\Shorter\DomainEditRequest;
use App\Http\Requests\Shorter\DomainRequest;
use App\Http\Requests\Shorter\UrlRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Shorter\DomainService;
use App\Services\Shorter\UrlService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShorterController extends Controller
{
    public function __construct(
        public readonly DomainService $domainService,
        public readonly UrlService    $urlService
    )
    {
    }

    public function showShortUrl(): Response
    {
        return Inertia::render('Shorter/ShortUrl', [
            'domains' => $this->domainService->getEnabledDomains(),
        ]);
    }

    public function createShortUrl(UrlRequest $request): JsonResponse
    {
        try {
            $urlDto = new UrlCreateDto(
                original_url: $request->validated('original_url'),
                domain: $request->validated('domain'),
                short_code: $request->validated('short_code'),
            );

            $shortUrl = $this->urlService->createShortUrl($urlDto);

            return ApiResponse::created([
                'short_url' => $shortUrl,
                'message' => 'Short link created successfully',
            ]);

        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage());

        } catch (\Throwable $e) {
            report($e);
            return ApiResponse::serverError('Failed to create short link.');
        }
    }


    public function showManagerUrl(Request $request): Response
    {
        $page = $request->integer('page', 1);
        $search = $request->string('search');


        return Inertia::render('Shorter/ManagerUrl', [
            'urls' => $this->urlService->getUserUrlsPaginated(page: $page, search: $search),
            'domains' => $this->domainService->getEnabledDomains(),
        ]);
    }

    public function editUrl(UrlRequest $request, int $urlId): JsonResponse
    {
        $url = $this->urlService->getUrl($urlId);

        if (!Guard::owns($url)) {
            return ApiResponse::forbidden('You do not have permission to edit this url.');
        }

        $validated = $request->validated();

        $urlDto = new UrlCreateDTO(
            original_url: $validated['original_url'],
            domain: $validated['domain'],
            short_code: $validated['short_code'],
            id: $urlId
        );

        try {
            return ApiResponse::created([
                'url' => $this->urlService->editShortUrl($urlDto),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to edit short link.');
        }
    }

    public function deleteUrl(int $urlId): JsonResponse
    {
        $url = $this->urlService->getUrl($urlId);

        if (!Guard::owns($url)) {
            return ApiResponse::forbidden('You do not have permission to edit this url.');
        }

        try {
            $this->urlService->deleteShortUrl($urlId);
            return ApiResponse::success('Short link has been deleted.');
        } catch (Exception) {
            return ApiResponse::serverError('Failed to delete short link.');
        }
    }

    public function showDomains(Request $request): Response
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        return Inertia::render('Shorter/Domains', [
            'domains' => $this->domainService->getDomainsPaginated(page: $page, search: $search),
        ]);
    }

    public function createDomain(DomainRequest $request): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        $validated = $request->validated();

        $domainDto = new DomainCreateDTO(
            redirect_url: $validated['redirect_url'],
            domain: $validated['domain'],
        );

        try {
            return ApiResponse::created([
                'domain' => $this->domainService->createDomain($domainDto),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to create domain.');
        }
    }

    public function editDomain(DomainEditRequest $request, int $domainId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        $validated = $request->validated();

        $domainDto = new DomainCreateDTO(
            redirect_url: $validated['redirect_url'],
            id: $domainId,
            is_active: $validated['is_active'],
        );

        try {
            return ApiResponse::created([
                'domain' => $this->domainService->editDomain($domainDto),
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to edit domain.');
        }
    }

    public function deleteDomain(int $domainId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $this->domainService->deleteDomain($domainId);

            return ApiResponse::created([
                'message' => 'Domain deleted successfully',
            ]);
        } catch (Exception) {
            return ApiResponse::serverError('Failed to delete domain.');
        }
    }
}
