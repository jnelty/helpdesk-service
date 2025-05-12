<?php

namespace App\EventSubscriber;

use App\Entity\Ticket;
use App\Entity\TicketStatus;
use App\Exceptions\ApiException;
use App\Exceptions\TicketStatusNotFoundException;
use App\Repository\TicketStatusRepository;
use App\Service\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\GuardEvent;

readonly class TicketStatusTransitionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface   $entityManager,
        private TicketService $ticketService
    )
    {
    }

    public function onGuard(GuardEvent $event): void
    {
        /** @var Ticket $ticket */
        $ticket = $event->getSubject();
        $ticketStatus = $ticket->getStatus();

        if (! $ticketStatus) {
            $exceptionData = new TicketStatusNotFoundException(
                Response::HTTP_BAD_REQUEST,
                "TicketStatusNotFound",
                "Ticket status not found",
            );

            throw new ApiException($exceptionData);
        }
    }

    public function onCompleted(CompletedEvent $event): void
    {
        /** @var Ticket $ticket */
        $ticket = $event->getSubject();

        /** @var TicketStatusRepository $ticketStatusRepository */
        $ticketStatusRepository = $this->entityManager
            ->getRepository(TicketStatus::class);

        $ticketStatus = $ticketStatusRepository
            ->findOneBy([
                "name" => $ticket->getCurrentPlace()
            ]);

        if (! $ticketStatus) {
            $exceptionData = new TicketStatusNotFoundException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                "TicketStatusNotFound",
                "Ticket status not found. Can not make transition",
            );

            throw new ApiException($exceptionData);
        }

        $this->ticketService->updateStatus($ticket, $ticketStatus);
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.ticket_status.guard' => ['onGuard'],
            'workflow.ticket_status.completed' => ['onCompleted'],
        ];
    }
}