<?php

namespace App\Enum;

enum TicketStatusEnum: string
{
    case NEW = 'new';
    case OPEN = 'open';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public static function names(): array
    {
        return array_map('strtolower', array_column(self::cases(), 'name'));
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}
