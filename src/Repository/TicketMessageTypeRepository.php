<?php

namespace App\Repository;

use App\Entity\TicketMessageType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketMessageType>
 */
class TicketMessageTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketMessageType::class);
    }

    public function getOrCreate(string $type)
    {
        $ticketMessageType = $this->findOneBy(['name' => $type]);

        if (! $ticketMessageType) {
            $ticketMessageType = new TicketMessageType();
            $ticketMessageType->setName($type);

            $this->entityManager->persist($ticketMessageType);
        }
    }

    //    /**
    //     * @return TicketMessageType[] Returns an array of TicketMessageType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TicketMessageType
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
