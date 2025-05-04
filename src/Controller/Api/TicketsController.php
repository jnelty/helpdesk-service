<?php

namespace App\Controller\Api;

use App\DTO\Ticket\CreateTicketDTO;
use App\DTO\Ticket\CreateTicketMessageDTO;
use App\DTO\Ticket\TransitionDTO;
use App\Entity\Ticket;
use App\Entity\User;
use App\Service\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class TicketsController extends AbstractController
{
    public function __construct(
        #[Target('ticket_status')]
        private WorkflowInterface $workflow,

        private EntityManagerInterface $entityManager,
        private TicketService $ticketService,
        private SerializerInterface $serializer,
    )
    {
    }


    #[Route('/api/tickets', name: 'storeTickets', methods: ['POST'])]
    public function store(
        #[ValueResolver('dto')] CreateTicketDTO $createTicketDTO
    ): JsonResponse
    {
        $ticket = $this->ticketService->store($createTicketDTO);

        $ticketData = $this->serializer->normalize(
            data: $ticket,
            context: [
                '_format' => 'json',
                'groups' => ['index-view', 'tag-view']
            ]
        );

        return new JsonResponse($ticketData, Response::HTTP_OK);
    }

    #[Route(
        path: '/api/tickets',
        name: 'indexTickets',
        methods: ['GET'],
    )]
    public function index(
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)] ?string $status,
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)] ?array $tags,
        #[MapQueryParameter(
            options: ['min_range' => 1],
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY
        )] int $page = 1,
        #[MapQueryParameter(
            options: ['min_range' => 2, 'max_range' => 50],
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY
        )] int $limit = 20
    ): JsonResponse
    {
        $tickets = $this->entityManager->getRepository(Ticket::class)->findByParams(
            params: [
                'status' => $status,
                'tags' => $tags,
            ],
            pageOptions: [
                'page' => $page,
                'limit' => $limit
            ]
        );

        $ticketData = $this->serializer->normalize(
            data: $tickets,
            context: [
                '_format' => 'json',
                'groups' => ['index-view', 'tag-view']
            ]
        );

        return new JsonResponse([
            'items' => $ticketData,
            'total' => count($ticketData),
            'page' => $page,
            'limit' => $limit
        ], Response::HTTP_OK);

    }

    #[Route('/api/tickets/{id}', name: 'showTicket', methods: ['GET'])]
    public function show(Ticket $ticket): JsonResponse
    {
        $ticketData = $this->serializer->normalize(
            data: $ticket,
            context: [
                '_format' => 'json',
                'groups' => ['store-view', 'tag-view']
            ]
        );

        return new JsonResponse($ticketData, Response::HTTP_OK);
    }

    #[Route('/api/tickets/{id}/messages', name: 'createTicketMessage', methods: ['POST'])]
    public function createMessage(
        #[ValueResolver('dto')] CreateTicketMessageDTO $createTicketMessageDTO,
        Ticket $ticket
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $ticketMessage = $this->ticketService->addTicketMessage(
            createTicketMessageDTO: $createTicketMessageDTO,
            ticket: $ticket,
            user: $user
        );

        $messageData = $this->serializer->normalize(
            data: $ticketMessage,
            context: [
                '_format' => 'json',
                'groups' => ['message-view']
            ]
        );

        return new JsonResponse($messageData);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/api/tickets/{id}/transition', name: 'transitStatusTicket', methods: ['POST'])]
    public function transit(
        #[ValueResolver('dto')] TransitionDTO $transitionDTO,
        Ticket $ticket
    ): JsonResponse
    {
        $status = $ticket->getStatusEntity();
        $ticket->setCurrentPlace($status->getName());

        if ($this->workflow->can($ticket, $transitionDTO->transition)) {
            $this->workflow->apply($ticket, $transitionDTO->transition);

            return new JsonResponse(
                [
                    'id' => $ticket->getId(),
                    'oldStatus' => $status->getName(),
                    'newStatus' => $ticket->getStatus()
                ],
                Response::HTTP_OK
            );
        } else {
            return new JsonResponse(
                [
                    'item' => [
                        'id' => $ticket->getId(),
                        'status' => $ticket->getStatus(),
                    ],
                    'message' => 'Forbidden transition to specified state'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
