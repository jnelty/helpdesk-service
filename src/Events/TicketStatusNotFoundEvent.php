<?php

namespace App\Events;

class TicketStatusNotFoundEvent
{
    public const NAME = 'ticket_status.not_found';

    public function __construct()
    {
    }
}