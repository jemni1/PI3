<?php

namespace App\Repository;

use App\Entity\Commandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commandes>
 */
class CommandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandes::class);
    }
  
    public function findMonthlySales(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                MONTH(c.date) as month,
                YEAR(c.date) as year,
                COUNT(c.id) as nombreVentes,
                SUM(c.prix * c.quantite) as revenuTotal
            FROM commandes c
            WHERE c.statut = :statut
            GROUP BY YEAR(c.date), MONTH(c.date)
            ORDER BY YEAR(c.date) DESC, MONTH(c.date) DESC
            LIMIT 12
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['statut' => 'complété']);

        return $result->fetchAllAssociative();
    }

    public function findTopProducts(int $limit = 5): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                c.nom,
                SUM(c.quantite) as totalVendus,
                SUM(c.prix * c.quantite) as revenuTotal
            FROM commandes c
            WHERE c.statut = :statut
            GROUP BY c.id_produit, c.nom
            ORDER BY totalVendus DESC
            LIMIT :limit
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'statut' => 'complété',
            'limit' => $limit
        ]);

        return $result->fetchAllAssociative();
    }

    public function findDailySales(int $days = 7): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                DATE(c.date) as date,
                COUNT(c.id) as nombreVentes,
                SUM(c.prix * c.quantite) as revenuTotal
            FROM commandes c
            WHERE c.statut = :statut
                AND c.date >= DATE_SUB(CURRENT_DATE(), INTERVAL :days DAY)
            GROUP BY DATE(c.date)
            ORDER BY date DESC
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'statut' => 'complété',
            'days' => $days
        ]);

        return $result->fetchAllAssociative();
    }

    public function findStatutStats(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                c.statut,
                COUNT(c.id) as nombre
            FROM commandes c
            GROUP BY c.statut
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
}