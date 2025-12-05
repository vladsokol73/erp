<?php

namespace App\Services\Operator;

use App\DTO\Operator\OperatorStatisticListDto;
use App\DTO\Operator\OperatorStatisticTotalsDto;
use App\DTO\PaginatedListDto;
use App\Models\Operator\OperatorChannelStatistic;
use App\Models\Operator\OperatorStatistic;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperatorStatisticService
{
    public function getStatisticPaginated(array $filters, int $page, int $perPage = 50, User|null $user = null): PaginatedListDto
    {
        $filters = $this->normalizeDateFilters($filters);
        $user = $user ?? Auth::user();

        $useChannel = $this->useChannelStatistic($filters, $user);
        $isSingleDay = ($filters['date']['from'] ?? '') === ($filters['date']['to'] ?? '');

        $query = $this
            ->resolveQueryBuilder($filters, $user, $useChannel)
            ->filter($filters);

        $query = $this->applyAggregationIfNeeded(clone $query, $useChannel, $isSingleDay);

        $paginator = $query
            ->with($this->withOperatorWithRetention($filters))
            ->paginate($perPage, ['*'], 'page', $page);

        return PaginatedListDto::fromPaginator(
            $paginator,
            fn($item) => $this->mapStatisticItemToDto($item, $useChannel, $isSingleDay)
        );
    }


    private function resolveQueryBuilder(array $filters, User $user, bool $useChannel): Builder
    {
        return $useChannel
            ? OperatorChannelStatistic::query()
                ->forUserAvailableOperators($user)
                ->forUserAvailableChannels($user)
            : OperatorStatistic::query()
                ->onlyOperators()
                ->forUserAvailableOperators($user);
    }

    private function applyAggregationIfNeeded(Builder $query, bool $useChannel, bool $isSingleDay): Builder
    {
        if (!$useChannel && $isSingleDay) {
            return $query;
        }

        return $query
            ->selectRaw('
            ' . ($useChannel
                    ? 'operator_id, MIN(operator_id) as entity_id, channel_id'
                    : 'entity_id as operator_id, MIN(entity_id) as entity_id') . ',
            MIN(id) as id,
            MIN(start_time) as start_time,
            MAX(end_time) as end_time,
            SUM(new_client_chats) as new_client_chats,
            SUM(total_clients) as total_clients,
            SUM(inbox_messages) as inbox_messages,
            SUM(outbox_messages) as outbox_messages,
            SUM(total_time) as total_time,
            SUM(reg_count) as reg_count,
            SUM(dep_count) as dep_count
            ' . ($useChannel ? '' : ', SUM(fd) as fd') . '
        ')
            ->groupBy($useChannel ? ['operator_id', 'channel_id'] : 'entity_id');
    }

    private function mapStatisticItemToDto($item, bool $useChannel, bool $isSingleDay): OperatorStatisticListDto
    {
        $fd = (int) ($item->fd ?? 0);
        $totalClients = (int) ($item->total_clients ?? 0);
        $crDialogToFd = $totalClients > 0 ? round(($fd / $totalClients) * 100, 2) : 0.0;
        return new OperatorStatisticListDto(
            id: (int) $item->id,
            operator_id: $useChannel ? (int) $item->operator_id : (int) $item->entity_id,
            new_client_chats: (int) $item->new_client_chats,
            total_clients: (int) $item->total_clients,
            inbox_messages: (int) $item->inbox_messages,
            outbox_messages: (int) $item->outbox_messages ,
            total_time: (int) $item->total_time,
            reg_count: (int) $item->reg_count,
            dep_count: (int) $item->dep_count,
            start_time: $item->start_time,
            end_time: $item->end_time,
            operator_name: $item->operator->name ?? null,
            operator_score: round($item->operator?->aiRetentionReports?->avg('score') ?? 0, 1),
            fd: $fd,
            cr_dialog_to_fd: $crDialogToFd,
        );
    }

    public function getStatisticTotals(array $filters, ?User $user = null): OperatorStatisticTotalsDto
    {
        $user = $user ?? Auth::user();
        $useChannel = $this->useChannelStatistic($filters, $user);

        $query = $useChannel
            ? OperatorChannelStatistic::query()
                ->forUserAvailableOperators($user)
                ->forUserAvailableChannels($user)
            : OperatorStatistic::query()
                ->onlyOperators()
                ->forUserAvailableOperators($user);

        $query->filter($filters);

        return new OperatorStatisticTotalsDto(
            all_clients: (int) $query->sum('total_clients'),
            all_new_clients: (int) $query->sum('new_client_chats'),
        );
    }

    public function getStatisticLatestUpdatedAt(User|null $user = null): Carbon
    {
        $user = $user ?? Auth::user();

        return OperatorStatistic::forUserAvailableOperators($user)
            ->latest('updated_at')
            ->value('updated_at')?->startOfHour()
            ?? now()->startOfHour();
    }

    private function normalizeDateFilters(array $filters): array
    {
        // Если нет даты — устанавливаем текущую
        if (empty($filters['date']['from']) && empty($filters['date']['to'])) {
            $today = now()->toDateString();
            $filters['date']['from'] = $today;
            $filters['date']['to'] = $today;
        }

        return $filters;
    }

    private function useChannelStatistic(array $filters, User $user): bool
    {
        // Принудительное включение канальной статистики через фильтр
        $isForcedEnabled = ($filters['filter_mode_channels'] ?? 'off') === 'on';

        // Пользователь явно выбрал каналы для фильтрации
        $hasExplicitChannelFilter = !empty($filters['channels']) && $filters['channels'] !== ['all'];

        // Пользователь не имеет доступа ко всем каналам
        $userHasLimitedChannels = !in_array('all', $user->available_channels ?? [], true);

        return $isForcedEnabled || $hasExplicitChannelFilter || $userHasLimitedChannels;
    }

    /**
     * Загружает оператора с ограниченными по дате AI-отчётами.
     */
    private function withOperatorWithRetention(array $filters): array
    {
        $dateFrom = $filters['date']['from'] ?? null;
        $dateTo   = $filters['date']['to'] ?? null;

        return [
            'operator' => function ($operatorQuery) use ($dateFrom, $dateTo) {
                $operatorQuery
                    ->with([
                        'aiRetentionReports' => function ($q) use ($dateFrom, $dateTo) {
                            $q->dateRange($dateFrom, $dateTo)
                                ->whereNotNull('score')
                                ->where('score', '>', 0);
                        },
                    ])
                    ->withAvg([
                        'aiRetentionReports as operator_score' => function ($q) use ($dateFrom, $dateTo) {
                            $q->dateRange($dateFrom, $dateTo)
                                ->whereNotNull('score')
                                ->where('score', '>', 0);
                        },
                    ], 'score');
            },
        ];
    }


}
