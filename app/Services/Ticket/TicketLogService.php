<?php

namespace App\Services\Ticket;

use App\DTO\Ticket\TicketLogCreateDto;
use App\Models\Ticket\TicketLog;
use Illuminate\Database\Eloquent\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

class TicketLogService
{
    /**
     * Создание нового лога тикета
     */
    public function createLog(TicketLogCreateDto $dto): TicketLog
    {
        $log = new TicketLog([
            'ticket_id' => $dto->ticket_id,
            'user_id' => $dto->user_id,
            'action' => $dto->action,
            'old_values' => $dto->old_values,
            'new_values' => $dto->new_values,
        ]);
        $log->save();

        return $log;
    }

    /**
     * Получение логов по ID тикета
     */
    public function getLogsByTicketId(int $ticketId): Collection
    {
        return TicketLog::where('ticket_id', $ticketId)
            ->orderByDesc('created_at')
            ->get();
    }
}
