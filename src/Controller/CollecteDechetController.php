<?php
namespace App\Controller;

use App\Entity\CollecteDechet;
use App\Form\CollecteDechetType;
use App\Repository\CollecteDechetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\TerrainsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Notification;


class CollecteDechetController extends AbstractController
{
    #[Route('/collecte', name: 'collecte_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, CollecteDechetRepository $collecteRepo): Response
    {
        
        $collecte = new CollecteDechet();
        $form = $this->createForm(CollecteDechetType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                // Déplacer le fichier dans le répertoire public/uploads/collecte_img
                $imageFile->move(
                    $this->getParameter('imgs_directory'),
                    $newFilename
                );
    
                // Mettre à jour l'entité avec le nom du fichier
                $collecte->setImageUrl($newFilename);
            }
    
            $entityManager->persist($collecte);
            $entityManager->flush();
                       // Création de la notification
                       $notification = new Notification();
                       $notification->setMessage('Une nouvelle collecte a été ajoutée.');
                       $notification->setCreatedAt(new \DateTime());
                       $notification->setIsRead(false);
                       $entityManager->persist($notification);
                       $entityManager->flush();
            

            $this->addFlash('success', 'Collecte enregistrée avec succès !');
            return $this->redirectToRoute('collecte_new');
        }

        return $this->render('collecte/index.html.twig', [
            'form' => $form->createView(),
            'collectes' => $collecteRepo->findAll(),
        ]);
    }

    #[Route('/collecte/{id}/edit', name: 'collecte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CollecteDechet $collecte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollecteDechetType::class, $collecte);
        $form->handleRequest($request);

        $currentImage = $collecte->getImageUrl(); // Stocker l'image actuelle au cas où on ne la remplace pas

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Gérer l'upload de la nouvelle image
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire public/uploads/collecte_img
                $imageFile->move(
                    $this->getParameter('imgs_directory'),
                    $newFilename
                );

                // Supprimer l'ancienne image si elle existe
                if ($currentImage) {
                    $oldImagePath = $this->getParameter('imgs_directory') . '/' . $currentImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Mettre à jour l'entité avec le nom du fichier de la nouvelle image
                $collecte->setImageUrl($newFilename);
            }

            // Sauvegarder les modifications
            $entityManager->flush();
            $this->addFlash('success', 'Collecte modifiée avec succès !');

            return $this->redirectToRoute('collecte_new');
        }

        return $this->render('collecte/edit.html.twig', [
            'form' => $form->createView(),
            'collecte' => $collecte,
        ]);
    }

    #[Route('/collecte/{id}/delete', name: 'collecte_delete', methods: ['POST'])]
    public function delete(Request $request, CollecteDechet $collecte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $collecte->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collecte);
            $entityManager->flush();

            $this->addFlash('success', 'Collecte supprimée avec succès !');
        }

        return $this->redirectToRoute('collecte_new');
    }

    #[Route('/collecte/{id}', name: 'collecte_show')]
    public function show(CollecteDechet $collecte): Response
    {
        return $this->render('collecte/show.html.twig', [
            'collecte' => $collecte,
        ]);
    }

    #[Route('/recherche-collecte', name: 'search_collecte', methods: ['GET'])]
public function searchCollecte(Request $request, CollecteDechetRepository $collecteRepo): JsonResponse
{
    $searchTerm = $request->query->get('term', '');

    $collectes = $collecteRepo->createQueryBuilder('c')
        ->where('c.typeDechet LIKE :term') // Remplace "nom" par le champ que tu veux rechercher
        ->setParameter('term', '%' . $searchTerm . '%')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();

    $results = [];
    foreach ($collectes as $collecte) {
        $results[] = [
            'id' => $collecte->getId(),
            'text' => $collecte->getTypeDechet(), // Remplace "nom" par le champ affiché
        ];
    }

    return new JsonResponse($results);
}
}
