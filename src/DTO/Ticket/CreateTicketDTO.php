<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
readonly class CreateTicketDTO implements DtoResolvedInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 64,
            maxMessage: 'Title cannot be longer than {{ limit }} characters',
        )]
        public ?string $title,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 1024,
            maxMessage: 'Description cannot be longer than {{ limit }} characters',
        )]
        public ?string $description,

        #[Assert\Type('array')]
        public ?array $tags,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Email(
            message: 'The email {{ value }} is not a valid email.',
        )]
        public ?string $requesterEmail
    ) {
    }
}