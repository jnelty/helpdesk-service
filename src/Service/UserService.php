<?php

namespace App\Service;

use App\DTO\User\CreateUserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function store(
        EntityManagerInterface $entityManager,
        CreateUserDTO $createUserDTO,
    ): User
    {
        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $createUserDTO->password
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($createUserDTO->email);
        $user->setFirstName($createUserDTO->firstName);
        $user->setLastName($createUserDTO->lastName);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}