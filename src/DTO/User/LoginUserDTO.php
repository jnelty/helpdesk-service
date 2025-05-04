<?php

namespace App\DTO\User;

use App\Validator\EmailNotExists;
use Symfony\Component\Validator\Constraints as Assert;

class LoginUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\NotNull]
        #[Assert\Type('string')]
        #[Assert\Email]
        public ?string $email,

        #[Assert\NotBlank]
        #[Assert\NotNull]
        #[Assert\Type('string')]
        public ?string $password,
    )
    {
    }
}