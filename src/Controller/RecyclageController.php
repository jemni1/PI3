<?php

namespace App\Controller;

use App\Entity\RecyclageDechet;
use App\Entity\CollecteDechet;
use App\Form\RecyclageDechetType;
use App\Service\UnsplashService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecyclageController extends AbstractController
{
    private UnsplashService $unsplashService;

    // Inject UnsplashService in the constructor
    public function __construct(UnsplashService $unsplashService)
    {
        $this->unsplashService = $unsplashService;
    }

    #[Route('/admin/recyclage', name: 'admin_recyclage_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $recyclages = $entityManager->getRepository(RecyclageDechet::class)->findAll();
        $collectes = $entityManager->getRepository(CollecteDechet::class)->findAll();

        return $this->render('recyclage/index.html.twig', [
            'recyclages' => $recyclages,
            'collectes' => $collectes,
        ]);
    }

    #[Route('/admin/recyclage/new', name: 'admin_recyclage_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recyclageDechet = new RecyclageDechet();
        $form = $this->createForm(RecyclageDechetType::class, $recyclageDechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('admin_recyclage_list');
                }

                $recyclageDechet->setImageUrl($newFilename);
            } else {
                // Si aucune image n'est fournie, générer une image aléatoire via Unsplash
                $randomImage = $this->unsplashService->getRandomImage('recyclage');
                $recyclageDechet->setImageUrl($randomImage);  // Assurez-vous que $randomImage contient une URL complète

            }

            // Associer les collectes au recyclage
            $collectes = $form->get('collectes')->getData();
            foreach ($collectes as $collecte) {
                $recyclageDechet->addCollecte($collecte);
            }

            // Sauvegarder le recyclage
            $entityManager->persist($recyclageDechet);
            $entityManager->flush();

            $this->addFlash('success', 'Recyclage ajouté avec succès !');
            return $this->redirectToRoute('admin_recyclage_list');
        }

        return $this->render('recyclage/recyclage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/recyclage/edit/{id}', name: 'admin_recyclage_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, RecyclageDechet $recyclageDechet): Response
    {
        // Créer le formulaire pour l'édition
        $form = $this->createForm(RecyclageDechetType::class, $recyclageDechet);
        $form->handleRequest($request);

        $currentImage = $recyclageDechet->getImageUrl(); // Stocker l'image actuelle

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image si une nouvelle image est téléchargée
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Si une nouvelle image est téléchargée, gérer le remplacement de l'image
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    // Déplacer le fichier dans le répertoire public/uploads/recyclage_img
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('admin_recyclage_list');
                }

                // Supprimer l'ancienne image si elle existe
                if ($currentImage) {
                    $oldImagePath = $this->getParameter('image_directory') . '/' . $currentImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Mettre à jour le nom du fichier dans l'entité
                $recyclageDechet->setImageUrl($newFilename);
            }

            // Mettre à jour les collectes
            foreach ($recyclageDechet->getCollectes() as $collecte) {
                $collecte->setRecyclageDechet($recyclageDechet);
            }

            // Sauvegarder les changements dans la base de données
            $entityManager->flush();
            $this->addFlash('success', 'Recyclage modifié avec succès !');
            return $this->redirectToRoute('admin_recyclage_list');
        }

        return $this->render('recyclage/recyclage.html.twig', [
            'form' => $form->createView(),
            'recyclageDechet' => $recyclageDechet,
        ]);
    }

    #[Route('/admin/recyclage/delete/{id}', name: 'admin_recyclage_delete')]
    public function delete(EntityManagerInterface $entityManager, RecyclageDechet $recyclageDechet): Response
    {
        $entityManager->remove($recyclageDechet);
        $entityManager->flush();

        $this->addFlash('success', 'Recyclage supprimé avec succès !');
        return $this->redirectToRoute('admin_recyclage_list');
    }

    #[Route('/admin/recyclage/show/{id}', name: 'admin_recyclage_show')]
    public function show(RecyclageDechet $recyclageDechet): Response
    {
        return $this->render('recyclage/show.html.twig', [
            'recyclage' => $recyclageDechet,
        ]);
    }
}
