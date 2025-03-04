<?php

namespace App\Repository;

use App\Entity\RecyclageDechet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecyclageDechet>
 */
class RecyclageDechetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecyclageDechet::class);
    }
    public function getEnergyUsageStats(): array
{
    return $this->createQueryBuilder('r')
        ->select('r.usageEnergie AS usage_energie, SUM(r.energieProduite) AS total_energie')
        ->groupBy('r.usageEnergie')
        ->getQuery()
        ->getResult();
}

    //    /**
    //     * @return RecyclageDechet[] Returns an array of RecyclageDechet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RecyclageDechet
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function filterRecyclages(?string $criteria, ?string $dateFilter): array
{
    $qb = $this->createQueryBuilder('r');

    if ($criteria === 'highQuantity') {
        $qb->andWhere('r.quantiteRecyclee >= 50');
    } elseif ($criteria === 'lowQuantity') {
        $qb->andWhere('r.quantiteRecyclee < 50');
    } elseif ($criteria === 'highEnergy') {
        $qb->andWhere('r.energieProduite >= 100');
    } elseif ($criteria === 'lowEnergy') {
        $qb->andWhere('r.energieProduite < 100');
    }

    if ($dateFilter) {
        $qb->andWhere('r.dateRecyclage = :dateFilter')
           ->setParameter('dateFilter', $dateFilter);
    }

    return $qb->getQuery()->getResult();
}

}
