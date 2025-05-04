<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tags')]
class Tag
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Ticket::class, inversedBy: 'tags')]
    #[ORM\JoinTable(name: "tag_ticket")]
    #[ORM\JoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    #[Groups(["tag-view"])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(["tag-view"])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTickets(): array
    {
        return $this->tickets->toArray();
    }

    public function setTickets(Collection $tickets): void
    {
        $this->tickets = $tickets;
    }

    public function addTicket(Ticket $ticket): void
    {
        $this->tickets[] = $ticket;
    }
}
