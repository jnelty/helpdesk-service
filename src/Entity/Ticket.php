<?php

namespace App\Entity;

use App\Events\TicketStatusNotFoundEvent;
use App\Repository\TicketRepository;
use App\Repository\TicketStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Workflow\WorkflowInterface;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'tickets')]
    private Collection $tags;
    #[ORM\ManyToOne(targetEntity: TicketStatus::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: "status_id", referencedColumnName: 'id', nullable: false)]
    private ?TicketStatus $status = null;

    public string $currentPlace;

    /**
     * @var Collection<int, TicketMessage>
     */
    #[ORM\OneToMany(targetEntity: TicketMessage::class, mappedBy: 'ticket', orphanRemoval: true)]
    private Collection $ticketMessages;
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->tags = new ArrayCollection();
        $this->ticketMessages = new ArrayCollection();
    }

    #[Groups(["public-view"])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(["public-view"])]
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    #[Groups(["public-view"])]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    #[Groups(["public-view"])]
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[Groups(["public-view"])]
    public function getStatus(): string|null
    {
        return $this->status?->getName();
    }

    public function getStatusEntity(): ?TicketStatus
    {
        return $this->status;
    }

    public function setStatus(?TicketStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function addTag(Tag $tag): void
    {
        $tag->addTicket($this);

        $this->tags[] = $tag;
    }

    #[Groups(["public-view"])]
    public function getTags(): array
    {
        return $this->tags->toArray();
    }

    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    public function getCurrentPlace(): string
    {
        return $this->currentPlace;
    }

    public function setCurrentPlace(string $currentPlace, array $context = []): void
    {
        $this->currentPlace = $currentPlace;
    }

    #[Groups(["ticket-messages-view"])]
    public function getMessages(): array
    {
        return $this->ticketMessages->toArray();
    }

    /**
     * @return Collection<int, TicketMessage>
     */
    public function getTicketMessagesCollection(): Collection
    {
        return $this->ticketMessages;
    }

    public function addMessage(TicketMessage $ticketMessage): static
    {
        if (!$this->ticketMessages->contains($ticketMessage)) {
            $this->ticketMessages->add($ticketMessage);
            $ticketMessage->setTicket($this);
        }

        return $this;
    }

    public function removeTicketMessage(TicketMessage $ticketMessage): static
    {
        if ($this->ticketMessages->removeElement($ticketMessage)) {
            // set the owning side to null (unless already changed)
            if ($ticketMessage->getTicket() === $this) {
                $ticketMessage->setTicket(null);
            }
        }

        return $this;
    }
}
