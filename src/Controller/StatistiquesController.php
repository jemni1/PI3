<?php
namespace App\Controller;

use App\Entity\RecyclageDechet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'statistiques_index', methods: ['GET'])]
    public function getStatistiquesData(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les recyclages
        $recyclages = $entityManager->getRepository(RecyclageDechet::class)->findAll();
        
        // Préparer les données pour la vue
        $labels = [];
        $quantitesRecyclees = [];
        $energiesProduites = [];
        $usages = [];
    
        foreach ($recyclages as $recyclage) {
            $labels[] = $recyclage->getDateDebut()->format('d-m-Y') . ' → ' . $recyclage->getDateFin()->format('d-m-Y');
            $quantitesRecyclees[] = $recyclage->getQuantiteRecyclee();
            $energiesProduites[] = $recyclage->getEnergieProduite();
            $usages[] = $recyclage->getUtilisation();
        }
    
        // Rendre la vue Twig en passant les données
        return $this->render('recyclage/statistiques.html.twig', [
            'labels' => json_encode($labels),
            'quantites' => json_encode($quantitesRecyclees),
            'energies' => json_encode($energiesProduites),
            'usages' => json_encode($usages),
        ]);
    }
    
}
