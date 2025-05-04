<?php

namespace App\EventSubscriber;

use App\Entity\Ticket;
use App\Entity\TicketStatus;
use App\Events\TicketStatusNotFoundEvent;
use App\Exceptions\ApiException;
use App\Exceptions\UndefinedEntityFieldException;
use App\Repository\TicketStatusRepository;
use App\Service\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

readonly class TicketStatusTransitionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface   $entityManager,
        private EventDispatcherInterface $eventDispatcher,
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
            $this->eventDispatcher->dispatch(new TicketStatusNotFoundEvent());
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
            ->findByNameFieldOrNull($ticket->getCurrentPlace());

        if (! $ticketStatus) {
            $this->eventDispatcher->dispatch(new TicketStatusNotFoundEvent());
        }

        $this->ticketService->updateTicketStatus($ticket, $ticketStatus);
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.ticket_status.guard' => ['onGuard'],
            'workflow.ticket_status.completed' => ['onCompleted'],
        ];
    }
}