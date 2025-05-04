<?php

namespace App\Entity;

use App\Repository\TicketMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TicketMessageRepository::class)]
#[ORM\Table(name: 'ticket_messages')]
class TicketMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'ticketMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TicketMessageType $type = null;

    #[ORM\ManyToOne(inversedBy: 'ticketMessages')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'ticketMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct(
    )
    {
        $this->created_at = new \DateTimeImmutable();
    }

    #[Groups(["message-view"])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(["message-view"])]
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    #[Groups(["message-view"])]
    public function getType(): ?string
    {
        return $this->type?->getName();
    }

    public function setType(?TicketMessageType $type): static
    {
        $this->type = $type;

        return $this;
    }

    #[Groups(["message-view"])]
    public function getAuthor(): string
    {
        return $this->user->getFirstName();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    #[Groups(["message-view"])]
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
