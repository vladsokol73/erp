<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Facades\Guard;
use App\Http\Responses\ApiResponse;
use App\Services\Client\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct(
        public readonly ClientService $clientService,
    ) {}


    public function showAllClients(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CLIENTS_VIEW->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');
        $perPage = $request->integer('perPage', 10);

        return Inertia::render('Clients/AllClients', [
            'clients' => $this->clientService->getClientsPaginated(page: $page, search: $search, perPage: $perPage),
        ]);
    }

    public function showFailedJobs(): Response
    {
        return Inertia::render('Clients/FailedJobs');
    }

    public function showClientDetails(int $clientId): JsonResponse
    {
        $ClientDetailsDto = $this->clientService->getClientDetailsDto($clientId);

        return ApiResponse::success(
            [
                'client' => $ClientDetailsDto,
            ]
        );
    }

    public function showClientLogs(int $clientId): JsonResponse
    {
        $ClientLogsDto = $this->clientService->getClientLogsDto($clientId);

        return ApiResponse::success(
            [
                'logs' => $ClientLogsDto,
            ]
        );
    }
}
