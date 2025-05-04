<?php

namespace App\Service;

use App\Entity\TicketMessageType;
use Doctrine\ORM\EntityManagerInterface;

class TicketMessageService
{    public function __construct(
    private EntityManagerInterface $entityManager,
    public function getOrCreate(string $type): TicketMessageType
    {
        $ticketMessageType = $this->entityManager
            ->getRepository(TicketMessageType::class)
            ->findOneBy(['name' => $createTicketMessageDTO->type]);

        if (! $ticketMessageType) {
            $ticketMessageType = new TicketMessageType();
            $ticketMessageType->setName($createTicketMessageDTO->type);

            $this->entityManager->persist($ticketMessageType);
        }
    }
}