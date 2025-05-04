<?php

namespace App\DataFixtures;

use App\Entity\TicketStatus;
use App\Enum\TicketStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = TicketStatusEnum::values();

        foreach ($statuses as $status) {
            $ticketStatus = new TicketStatus();
            $ticketStatus->setName($status);

            $manager->persist($ticketStatus);
        }
        $manager->flush();
    }
}
