<?php

namespace App\DTO\Ticket;

use App\DTO\DtoResolvedInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTicketMessageDTO implements DtoResolvedInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 1024,
            maxMessage: 'Content cannot be longer than {{ limit }} characters',
        )]
        public ?string $content,

        #[Assert\NotBlank]
        #[Assert\Choice(
            choices: ['public', 'internal']
        )]
        public ?string $type,
    )
    {
    }
}