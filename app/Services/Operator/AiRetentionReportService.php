<?php

namespace App\Services\Operator;

use App\DTO\Operator\AiRetentionReportDto;
use App\DTO\Operator\AiRetentionReportListDto;
use App\DTO\PaginatedListDto;
use App\Jobs\ProcessAiRetentionReports;
use App\Jobs\ProcessAiRetentionReportsTest;
use App\Models\Operator\AiRetentionReport;
use App\Models\Operator\AiRetentionReportTest;
use App\Services\FlagService;

class AiRetentionReportService
{
    public function __construct(
        protected AiRetentionReportGenerator $aiRetentionReportGenerator,
        protected FlagService $flagService,
    )
    {
    }

    /**
     * Получить все AI-отчёты по оператору с опциональным фильтром по датам
     */
    public function getReportsByOperatorId(int $operatorId, ?string $from = null, ?string $to = null): array
    {
        [$from, $to] = $this->normalizeDates($from, $to);

        return AiRetentionReportDto::fromCollection(
            AiRetentionReport::query()
                ->where('operator_id', $operatorId)
                ->dateRange($from, $to)
                ->orderByDesc('conversation_date')
                ->get()
        );
    }

    /**
     * Получить один AI-отчёт по его ID
     */
    public function getReportById(int $reportId): AiRetentionReport
    {
        return AiRetentionReport::query()->findOrFail($reportId);
    }

    /**
     * Получить тестовые отчёты с пагинацией и поиском
     */
    public function getTestReportsPaginated(int $page, string $search): PaginatedListDto
    {
        $query = AiRetentionReportTest::query()
        ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: 10, page: $page),
            fn($report) => AiRetentionReportListDto::fromModel($report)
        );
    }

    /**
     * Удалить AI-отчёт по ID
     */
    public function deleteReport(int $reportId): void
    {
        $report = $this->getReportById($reportId);
        $report->delete();
    }

    /**
     * Создать новый AI-отчёт
     */
    public function createReport(array $data): AiRetentionReportDto
    {
        $report = AiRetentionReport::create($data);
        return AiRetentionReportDto::fromModel($report);
    }

    /**
     * Обновить AI-отчёт по ID
     */
    public function updateReport(int $reportId, array $data): AiRetentionReportDto
    {
        $report = $this->getReportById($reportId);
        $report->update($data);
        return AiRetentionReportDto::fromModel($report);
    }


    /**
     * Если даты не заданы, подставляет текущую дату для обеих.
     */
    private function normalizeDates(?string $from, ?string $to): array
    {
        if (empty($from) && empty($to)) {
            $today = now()->toDateString();
            return [$today, $today];
        }

        return [$from, $to];
    }

    public function generateTestReports(int $userId): void
    {
        ProcessAiRetentionReportsTest::dispatch(userId: $userId);
    }

    public function startReportJob():void
    {
        ProcessAiRetentionReports::dispatch()->onQueue('ai');
    }
}
