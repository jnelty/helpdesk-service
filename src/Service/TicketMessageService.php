<?php

namespace App\Service;

use App\DTO\Ticket\CreateTicketMessageDTO;
use App\Entity\TicketMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TicketMessageService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TicketMessageTypeService $ticketMessageTypeService
    )
    {
    }

    public function store(
        CreateTicketMessageDTO $createTicketMessageDTO,
        User $user
    ): TicketMessage
    {
        $ticketMessageType = $this->ticketMessageTypeService->getOrCreate($createTicketMessageDTO->type);

        $ticketMessage = new TicketMessage();
        $ticketMessage->setContent($createTicketMessageDTO->content);
        $ticketMessage->setType($ticketMessageType);
        $ticketMessage->setUser($user);
        $this->entityManager->persist($ticketMessage);

        return $ticketMessage;
    }
}