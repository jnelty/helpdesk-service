<?php

namespace App\DTO\Ticket;
use App\DTO\DtoResolvedInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TransitionDTO implements DtoResolvedInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice([
            'choices' => [
                'to_open',
                'to_resolved',
                'to_closed'
            ]
        ])]
        public ?string $transition,
    )
    {
    }
}