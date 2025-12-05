<?php

namespace App\Enums;

use App\DTO\Ticket\PlayerTicketStatusDto;

/**
 * Enum статусов PlayerTicket.
 * Значения = ключи из БД.
 */
enum PlayerTicketStatusEnum: string
{
    case ON_APPROVE = 'On Approve';
    case APPROVED   = 'Approved';
    case REJECTED   = 'Rejected';

    public function color(): string
    {
        return match ($this) {
            self::ON_APPROVE => 'amber',
            self::APPROVED   => 'emerald',
            self::REJECTED   => 'crimson',
        };
    }
}
