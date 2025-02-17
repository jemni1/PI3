<?php

namespace App\Controller;

use App\Entity\Terrains;
use App\Form\TerrainsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\RequestStack;

final class TerrainController extends AbstractController
{
    #[Route('/terrain', name: 'app_terrain')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $terrains = $entityManager->getRepository(Terrains::class)->findAll();

        return $this->render('terrain/index.html.twig', [
            'terrains' => $terrains,
        ]);
    }
   
    #[Route('/terrain/new', name: 'terrain_new')]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrains();
        $form = $this->createForm(TerrainsType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        }

        return $this->render('terrain/new.html.twig', [
            'form' => $form->createView(),
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
    public function update(Request $request, Terrains $terrain, EntityManagerInterface $entityManager): Response
    {
        $forme = $this->createForm(TerrainsType::class,$terrain);
        $forme->handleRequest($request);

        if ($forme->isSubmitted() && $forme->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Culture modifiée avec succès');
            return $this->redirectToRoute('app_terrain');
        }

        return $this->render('terrain/update.html.twig', [
            'forme' => $forme->createView(),
            'terrain' => $terrain
        ]);
    }
    #[Route('/terrain/edit/{id}', name: 'app_terrain_edit')]
    public function edit(int $id, Terrains $terrain, EntityManagerInterface $entityManager,RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $session->set('terrain_id', $id);
        
        return $this->redirectToRoute('app_all_products');
    }

}
