<?php

namespace App\Events;

class TicketStatusIsNotFoundEvent
{
    public const NAME = 'ticket_status.not_found';

    public function __construct()
    {
    }

    public function getDto(): PromotionEnquiryInterface
    {
        return $this->dto;
    }
}