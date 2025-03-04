<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

use App\Entity\Terrains;
use App\Form\TerrainsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CultureRepository;
use App\Service\CropPredictionService;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\TerrainsRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;


use Doctrine\ORM\Mapping\Entity;

final class TerrainController extends AbstractController
{
    #[Route('/terrain', name: 'app_terrain')]
    public function list(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $query = $em->getRepository(Terrains::class)->createQueryBuilder('t')->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Numéro de page (par défaut: 1)
            4 // Nombre d'éléments par page
        );

        return $this->render('terrain/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/terrain/edit/{id}', name: 'app_terrain_edit')]
    public function edit(int $id, Terrains $terrain, EntityManagerInterface $entityManager,RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $session->set('terrain_id', $id);
        
        return $this->redirectToRoute('app_all_products');
    }
    #[Route('/terrain/new', name: 'terrain_new')]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager,CultureRepository $cultureRepository): Response
    {
        $terrain = new Terrains();
        $form = $this->createForm(TerrainsType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all();
        $terrainData = $formData['terrains'] ?? [];
        if (isset($terrainData['latitude']) && isset($terrainData['longitude'])) {
            $terrain->setLatitude((float)$terrainData['latitude']);
            $terrain->setLongitude((float)$terrainData['longitude']);
        }
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('terrain_new');
                }

                $terrain->setImage($newFilename);
            }
            
    
            // Utilisation de l'EntityManager pour persister l'entité
            $entityManager->persist($terrain);
            $entityManager->flush();

            return $this->redirectToRoute('app_terrain');
        }$cultures = $cultureRepository->findAll();


        return $this->render('terrain/new.html.twig', [
            'form' => $form->createView(),
            'cultures' => $cultures,
        ]);
    }
    #[Route('/terrain/delete/{id}', name: 'app_terrain_delete')]
    public function delete(Terrains $terrain, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($terrain);
        $entityManager->flush();
        
        $this->addFlash('success', 'Terrain supprimée avec succès');
        return $this->redirectToRoute('app_terrain');
    }
    #[Route('/terrain/update/{id}', name: 'app_terrain_update')]
    public function update(Request $request, Terrains $terrain, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TerrainsType::class, $terrain);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all();
            $terrainData = $formData['terrains'] ?? [];
            if (isset($terrainData['latitude']) && isset($terrainData['longitude'])) {
                $terrain->setLatitude((float)$terrainData['latitude']);
                $terrain->setLongitude((float)$terrainData['longitude']);
            }
    
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_terrain_update', ['id' => $terrain->getId()]);
                }
    
                $terrain->setImage($newFilename);
            }
    
            // Enregistrement des modifications dans la base de données
            $entityManager->flush();
    
            // Message flash pour informer l'utilisateur du succès
            $this->addFlash('success', 'Le terrain a été modifié avec succès.');
    
            return $this->redirectToRoute('app_terrain');
        }
    
        return $this->render('terrain/update.html.twig', [
            'forme' => $form->createView(),
            'terrain' => $terrain
        ]);
    }
    #[Route('/terrain/map', name: 'map')]
    public function map(EntityManagerInterface $entityManager): Response
{
    $terrains = $entityManager
            ->getRepository(Terrains::class)
            ->findAll();
    
    // Préparer les données pour le JavaScript
    $terrainsData = [];
    foreach ($terrains as $terrain) {
        $cultures = [];
        foreach ($terrain->getCultures() as $culture) {
            $cultures[] = $culture->getNom(); // Ajustez selon votre modèle
        }
        
        $terrainsData[] = [
            'id' => $terrain-> getId(),
            'nom' => $terrain->getNom(),
            'adresse' => $terrain->getAdresse(),
            'superficie' => $terrain->getSuperficie(),
            'latitude' => $terrain->getLatitude(),
            'longitude' => $terrain->getLongitude(),
            'cultures' => $cultures,
            // Ajoutez d'autres propriétés selon vos besoins
          
        ];
    }
    
    return $this->render('terrain/mapp.html.twig', [
        'terrains' => $terrainsData,
    ]);
}
#[Route('/terrain/{id}', name: 'app_terrain_show')]
public function show(
    Terrains $terrain,
    HttpClientInterface $httpClient
): Response
{
    $weatherData = $terrain->getWeatherData($httpClient);
    
    return $this->render('terrain/temp.html.twig', [
        'terrain' => $terrain,
        'weatherData' => $weatherData,
    ]);
}
#[Route('/terrainsl', name: 'app_terrains_listl')]
public function listter(TerrainsRepository $terrainsRepository): Response
{
    $terrains = $terrainsRepository->findAll();
    return $this->render('terrain/list.html.twig', [
        'terrains' => $terrains,
    ]);
}
}