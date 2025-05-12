<?php

namespace App\Exceptions;

class TicketStatusNotFoundException extends ApiExceptionData
{
    private string $message;
    public function __construct(
        int $statusCode,
        string $type = "TicketStatusNotFound",
        string $message = "Ticket status not found",
    )
    {
        parent::__construct($statusCode, $type);

        $this->message = $message;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message
        ];
    }
}