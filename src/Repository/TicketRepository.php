<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findByParams(
        array $params,
        array $pageOptions
    ): array
    {
        extract($params);
        extract($pageOptions);

        $queryBuilder = $this->createQueryBuilder('t');

        if ($status) {
            $queryBuilder
                ->innerJoin('t.status', 's')
                ->where('s.name = :status')
                ->setParameter('status', $status);
        }

        if ($tags) {
            $queryBuilder
                ->join('t.tags', 'tags')
                ->where('tags.name IN (:tags)')
                ->setParameter('tags', $tags);
        }

        if ($limit && $page) {
            $offset = ($page - 1) * $limit;

            $queryBuilder
                ->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Ticket[] Returns an array of Ticket objects
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

    //    public function findOneBySomeField($value): ?Ticket
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
