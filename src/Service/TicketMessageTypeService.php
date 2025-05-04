<?php

namespace App\Service;

use App\Entity\TicketMessageType;
use Doctrine\ORM\EntityManagerInterface;

class TicketMessageTypeService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {

    }
    public function getOrCreate(string $type): TicketMessageType
    {
        $ticketMessageType = $this->entityManager
            ->getRepository(TicketMessageType::class)
            ->findOneBy(['name' => $type]);

        if (! $ticketMessageType) {
            $ticketMessageType = new TicketMessageType();
            $ticketMessageType->setName($type);

            $this->entityManager->persist($ticketMessageType);
        }

        return $ticketMessageType;
    }
}