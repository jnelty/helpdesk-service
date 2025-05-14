<?php

namespace App\Service\Serialize;

use App\Entity\Ticket;
use Symfony\Component\Serializer\SerializerInterface;

class TicketSerializer
{
    public function __construct(
        private SerializerInterface $serializer
    )
    {

    }
    public function serializeShowTicket(Ticket $ticket): array
    {
        return $this->serializer->normalize($ticket, context: [
            '_format' => 'json',
            'groups' => ['public-view', 'ticket-messages-view'],
        ]);
    }

    public function serializePublicView(Ticket $ticket): array
    {
        return $this->serializer->normalize($ticket, context: [
            '_format' => 'json',
            'groups' => ['public-view'],
        ]);
    }
}