<?php
namespace App\Controller;

use App\Repository\TerrainsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TerrainMapController extends AbstractController
{
    #[Route('/terrains/map', name: 'app_terrains_map')]
    public function index(TerrainsRepository $terrainRepository): Response
    {
        $terrains = $terrainRepository->findAll();
        
        return $this->render('terrain/map.html.twig', [
            'terrains' => $terrains,
        ]);
    }
    #[Route('/terrains/mape', name: 'app_terrains_map')]
    public function indexx(TerrainsRepository $terrainRepository): Response
    {
        $terrains = $terrainRepository->findAll();
        
        return $this->render('terrain/mapp.html.twig', [
            'terrains' => $terrains,
        ]);
    }
    
    #[Route('/api/terrains', name: 'api_terrains')]
    public function getTerrains(TerrainsRepository $terrainRepository): Response
    {
        $terrains = $terrainRepository->findAll();
        $data = [];
        
        foreach ($terrains as $terrain) {
            $data[] = [
                'id' => $terrain->getId(),
                'nom' => $terrain->getNom(),
                'adresse' => $terrain->getAdresse(),
                'superficie' => $terrain->getSuperficie(),
                'lat' => $terrain->getLatitude(),
                'lng' => $terrain->getLongitude(),
            ];
        }
        
        return $this->json($data);
    }
}