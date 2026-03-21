<?php

namespace App\Repository;

use App\Entity\Character;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Character>
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    /**
     * @return Character[]
     */
    public function findFilteredByUser(User $user, ?string $name, ?int $classId, ?int $raceId): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.characterClass', 'cc')
            ->leftJoin('c.race', 'r')
            ->addSelect('cc', 'r')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.name', 'ASC');

        if (null !== $name && '' !== $name) {
            $qb->andWhere('LOWER(c.name) LIKE :name')
                ->setParameter('name', '%'.mb_strtolower($name).'%');
        }

        if (null !== $classId) {
            $qb->andWhere('cc.id = :classId')
                ->setParameter('classId', $classId);
        }

        if (null !== $raceId) {
            $qb->andWhere('r.id = :raceId')
                ->setParameter('raceId', $raceId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Character[]
     */
    public function findForApi(?string $name, ?int $classId, ?int $raceId): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.characterClass', 'cc')
            ->leftJoin('c.race', 'r')
            ->addSelect('cc', 'r')
            ->orderBy('c.name', 'ASC');

        if (null !== $name && '' !== $name) {
            $qb->andWhere('LOWER(c.name) LIKE :name')
                ->setParameter('name', '%'.mb_strtolower($name).'%');
        }

        if (null !== $classId) {
            $qb->andWhere('cc.id = :classId')
                ->setParameter('classId', $classId);
        }

        if (null !== $raceId) {
            $qb->andWhere('r.id = :raceId')
                ->setParameter('raceId', $raceId);
        }

        return $qb->getQuery()->getResult();
    }
//    /**
//     * @return Character[] Returns an array of Character objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Character
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
