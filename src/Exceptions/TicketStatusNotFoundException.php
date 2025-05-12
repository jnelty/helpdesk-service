<?php

namespace App\Exceptions;

class TicketStatusNotFoundException extends ApiExceptionData
{
    private string $message;
    public function __construct(
        int $statusCode,
        string $message = "Ticket status not found",
        string $type = "TicketStatusNotFound"
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