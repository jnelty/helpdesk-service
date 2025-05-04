<?php

namespace App\DTO\User;

use App\DTO\DtoResolvedInterface;
use App\Validator\EmailNotExists;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDTO implements DtoResolvedInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\NotNull]
        #[Assert\Type('string')]
        #[Assert\Email]
        #[EmailNotExists]
        public ?string $email,

        #[Assert\NotBlank]
        #[Assert\NotNull]
        #[Assert\Type('string')]
        public ?string $password,

        #[Assert\NotBlank]
        #[Assert\NotNull]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 64,
            maxMessage: 'First name cannot be longer than {{ limit }} characters',
        )]
        public ?string $firstName,

        #[Assert\NotBlank]
        #[Assert\NotNull]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 64,
            maxMessage: 'Last name cannot be longer than {{ limit }} characters',
        )]
        public ?string $lastName
    ) {
    }
}