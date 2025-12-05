<?php

namespace App\Enums;

enum Chat2DeskEventEnum: string
{
    case INBOX = 'inbox';
    case OUTBOX = 'outbox';
    case COMMENT = 'comment';
    case NEW_CLIENT = 'new_client';
    case NEW_REQUEST = 'new_request';
    case ADD_TAG_TO_CLIENT = 'add_tag_to_client';
    case ADD_TAG_TO_REQUEST = 'add_tag_to_request';
    case DELETE_TAG_FROM_CLIENT = 'delete_tag_from_client';
    case DELETE_TAG_FROM_REQUEST = 'delete_tag_from_request';
    case CLIENT_UPDATED = 'client_updated';
    case CLOSE_DIALOG = 'close_dialog';
    case CLOSE_REQUEST = 'close_request';
    case DIALOG_TRANSFERRED = 'dialog_transferred';

    public static function clientEvents(): array
    {
        return [
            self::INBOX,
            self::OUTBOX,
            self::COMMENT,
            self::NEW_CLIENT,
            self::NEW_REQUEST,
            self::ADD_TAG_TO_CLIENT,
            self::ADD_TAG_TO_REQUEST,
            self::DELETE_TAG_FROM_CLIENT,
            self::DELETE_TAG_FROM_REQUEST,
            self::CLIENT_UPDATED,
            self::CLOSE_DIALOG,
            self::CLOSE_REQUEST,
            self::DIALOG_TRANSFERRED,
        ];
    }

    public static function operatorEvents(): array
    {
        return [
            self::INBOX,
            self::OUTBOX,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

