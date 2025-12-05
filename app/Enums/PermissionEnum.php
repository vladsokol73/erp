<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case SHORTER_SHOW = 'shorter.show';
    case TICKETS_MODERATE = 'tickets.moderation';
    case TICKETS_SETTINGS = 'tickets.settings';
    case CLIENTS_VIEW = 'clients.show';
    case CREATIVES_CREATE = 'creatives.create';
    case CREATIVES_UPDATE = 'creatives.update';
    case CREATIVES_TAGS = 'creatives.tags';
    case CREATIVES_COMMENTS = 'creatives.comments';
    case OPERATORS_VIEW = 'operators.show';
}
