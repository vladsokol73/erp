<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\ChannelService;
use App\Services\Operator\AiRetentionReportService;
use App\Services\Operator\OperatorService;
use App\Services\Operator\OperatorStatisticService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OperatorController extends Controller
{

    public function __construct(
        public readonly OperatorStatisticService $statisticService,
        public readonly OperatorService $operatorService,
        public readonly ChannelService $channelService,
        public readonly AiRetentionReportService $aiRetentionReportService
    ) {}

    public function showStatistic(Request $request): Response
    {
        $page = $request->integer('page', 1);
        $filter = $request->input('filter', []);
        $perPage = $request->integer('perPage', 50);

        return Inertia::render('Operators/Statistic', [
            'statistics' => fn() => $this->statisticService->getStatisticPaginated($filter, $page, $perPage),
            'statistic_updated_at' => fn() => $this->statisticService->getStatisticLatestUpdatedAt(),
            'statistic_totals' => fn() => $this->statisticService->getStatisticTotals($filter),

            'operators' => $this->operatorService->getOperators(),
            'channels' => $this->channelService->getChannels(),
        ]);
    }

    public function showReports(int $operatorId, Request $request): JsonResponse
    {
        $from = $request->input('date.from');
        $to = $request->input('date.to');

        $reportsDto = $this->aiRetentionReportService->getReportsByOperatorId($operatorId, $from, $to);

        return ApiResponse::success([
            'reports' => $reportsDto,
        ]);
    }

    public function showDashboard(): Response
    {
        return Inertia::render('Operators/Dashboard');
    }

    public function showOperators(): Response
    {
        return Inertia::render('AdminPanel/Operators');
    }
}
