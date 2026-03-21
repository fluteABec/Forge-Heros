<?php

namespace App\Repository;

use App\Entity\Party;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Party>
 */
class PartyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Party::class);
    }

    /**
     * @return Party[]
     */
    public function findFilteredByAvailability(?string $status): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.characters', 'c')
            ->addSelect('c')
            ->groupBy('p.id')
            ->orderBy('p.name', 'ASC');

        if ('available' === $status) {
            $qb->andHaving('COUNT(c.id) < p.maxSize');
        } elseif ('full' === $status) {
            $qb->andHaving('COUNT(c.id) >= p.maxSize');
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Party[] Returns an array of Party objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Party
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
