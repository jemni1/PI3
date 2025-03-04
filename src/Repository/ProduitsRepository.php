<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produits>
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }
    public function findAvailableProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.quantite > 0')
            ->getQuery()
            ->getResult();
    }
    public function findAvailableProductsss(?string $searchTerm = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.quantite > 0'); // Produits en stock

        if ($searchTerm) {
            $qb->andWhere('p.nom LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $qb->getQuery()->getResult();
    }
    public function findByName(string $searchTerm)
    {
        return $this->createQueryBuilder('p')
            ->where('p.nom LIKE :searchTerm')
            ->setParameter('searchTerm', $searchTerm . '%') // Recherche par préfixe
            ->getQuery()
            ->getResult();
    }
    public function findByPriceRange($min, $max)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.prix BETWEEN :min AND :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->getQuery()
            ->getResult();
    }
    


    //    /**
    //     * @return Produits[] Returns an array of Produits objects
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

    //    public function findOneBySomeField($value): ?Produits
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
