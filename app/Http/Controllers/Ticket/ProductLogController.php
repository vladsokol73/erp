<?php

namespace App\Http\Controllers\Ticket;

use App\DTO\PaginatedListDto;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Services\Log\ProductLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductLogController extends Controller
{
    public function __construct(
        public readonly ProductLogService $productLogService,
    ) {}

    public function showLogs(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::CHECK_PLAYER_MODERATION->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');
        $perPage = $request->integer('perPage', 10);

        if ($search->isEmpty()) {
            return Inertia::render('Tickets/CheckPlayer/ProductLogs', [
                'logs' => PaginatedListDto::empty(),
            ]);
        }

        return Inertia::render('Tickets/CheckPlayer/ProductLogs', [
            'logs' => $this->productLogService->getProductLogsPaginated($page, (string) $search, $perPage),
        ]);
    }
}


