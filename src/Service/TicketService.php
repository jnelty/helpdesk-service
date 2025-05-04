<?php

namespace App\Service;

use App\DTO\Ticket\CreateTicketDTO;
use App\DTO\Ticket\CreateTicketMessageDTO;
use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Entity\TicketMessageType;
use App\Entity\TicketStatus;
use App\Entity\User;
use App\Enum\TicketStatusEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class TicketService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagService $tagService,
        private TicketMessageService $ticketMessageService
    )
    {
    }

    public function store(
        CreateTicketDTO $createTicketDTO
    ): Ticket
    {
        $ticket = new Ticket();
        $newTicketStatus = $this->entityManager
            ->getRepository(TicketStatus::class)
            ->findByNameFieldOrNull(TicketStatusEnum::NEW->value);

        if (! $newTicketStatus) {
            $newTicketStatus = new TicketStatus();
            $newTicketStatus->setName(TicketStatusEnum::NEW->value);

            $this->entityManager->persist($newTicketStatus);
        }

        /** @var User $user */
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $createTicketDTO->requesterEmail]);
        $ticket->setUser($user);

        $tags = $this->tagService->getOrCreateManyTags($createTicketDTO->tags);
        foreach ($tags as $tag) {
            $ticket->addTag($tag);
        }

        $ticket->setDescription($createTicketDTO->description);
        $ticket->setTitle($createTicketDTO->title);
        $ticket->setStatus($newTicketStatus);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $ticket;
    }

    public function updateTicketStatus(
        Ticket $ticket,
        TicketStatus  $ticketStatus
    ): Ticket
    {
        $ticket->setStatus($ticketStatus);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $ticket;
    }

    public function addTicketMessage(
        CreateTicketMessageDTO $createTicketMessageDTO,
        Ticket $ticket,
        User $user
    ): Ticket
    {
        $ticketMessage = $this->ticketMessageService->store($createTicketMessageDTO, $user);
        $ticket->addTicketMessage($ticketMessage);
        $this->entityManager->persist($ticket);

        $this->entityManager->flush();

        return $ticket;
    }
}