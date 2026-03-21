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
        // Requete commune a la page web et a l'API pour filtrer les groupes sur leur capacite
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
}
