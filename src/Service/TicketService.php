<?php

namespace App\Service;

use App\DTO\Ticket\CreateTicketDTO;
use App\DTO\Ticket\CreateTicketMessageDTO;
use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Entity\TicketStatus;
use App\Entity\User;
use App\Enum\TicketStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TicketService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagService $tagService,
        private TicketMessageService $ticketMessageService,
        private TagAwareCacheInterface $cache,
        private SerializerInterface $serializer,
    )
    {
    }

    public function store(
        CreateTicketDTO $createTicketDTO,
        User $user
    ): Ticket
    {
        $ticket = new Ticket();
        $ticket->setUser($user);

        $newTicketStatus = $this->entityManager
            ->getRepository(TicketStatus::class)
            ->findOneBy([
                "name" => TicketStatusEnum::NEW->value
            ]);

        if (! $newTicketStatus) {
            $newTicketStatus = new TicketStatus();
            $newTicketStatus->setName(TicketStatusEnum::NEW->value);

            $this->entityManager->persist($newTicketStatus);
        }

        $tags = $this->tagService->getOrCreateManyTags($createTicketDTO->tags);
        foreach ($tags as $tag) {
            $ticket->addTag($tag);
        }

        $ticket->setDescription($createTicketDTO->description);
        $ticket->setTitle($createTicketDTO->title);
        $ticket->setStatus($newTicketStatus);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();
        $this->cache->invalidateTags(['tickets']);


        return $ticket;
    }

    public function updateStatus(
        Ticket $ticket,
        TicketStatus  $ticketStatus
    ): Ticket
    {
        $ticket->setStatus($ticketStatus);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();
        $this->cache->invalidateTags(['tickets']);

        return $ticket;
    }

    public function createMessage(
        CreateTicketMessageDTO $createTicketMessageDTO,
        Ticket $ticket,
        User $user
    ): TicketMessage
    {
        $ticketMessage = $this->ticketMessageService->store($createTicketMessageDTO, $user);
        $ticket->addMessage($ticketMessage);
        $this->entityManager->persist($ticket);

        $this->entityManager->flush();
        $this->cache->invalidateTags(['tickets']);

        return $ticketMessage;
    }


    public function fetchById(
        int $ticketId,
        array $serializerContext
    ): array
    {
        $cacheKey = 'tickets_'.$ticketId;

        $ticketData = $this->cache
            ->get($cacheKey, function (ItemInterface $item) use ($ticketId, $serializerContext): array|null {
                $item->tag(['tickets']);
                $item->expiresAfter(3600);

                $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);

                $ticketData = $this->serializer->normalize(
                    data: $ticket,
                    context: $serializerContext
                );

                return $ticketData;
            });

        if (! $ticketData) {
            throw new NotFoundHttpException('The requested ticket does not exist.');
        }

        return $ticketData;
    }

    public function fetchAll(
        array $query,
        array $serializerContext
    ): array
    {
        $cacheKey = 'tickets_' . md5(json_encode($query));
        $ticketsData = $this->cache
            ->get($cacheKey, function (ItemInterface $item) use ($query, $serializerContext): array|null {
                $item->tag(['tickets']);
                $item->expiresAfter(3600);

                $tickets = $this->entityManager->getRepository(Ticket::class)->findByParams(
                    params: [
                        'status' => $query['status'],
                        'tags' => $query['tags'],
                    ],
                    pageOptions: [
                        'page' => $query['page'],
                        'limit' => $query['limit']
                    ]
                );

                $ticketsData = $this->serializer->normalize(
                    data: $tickets,
                    context: $serializerContext
                );

                return $ticketsData;
        });


        return $ticketsData;
    }
}