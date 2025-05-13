<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userAdmin = new User();
        $userAdmin->setEmail('admin@mail.com');
        $userAdmin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $userAdmin->setFirstName('admin');
        $userAdmin->setLastName('admin');

        $hashedPassword = $this->passwordHasher->hashPassword($userAdmin, 'password');
        $userAdmin->setPassword($hashedPassword);

        $manager->persist($userAdmin);
        $manager->flush();
    }
}