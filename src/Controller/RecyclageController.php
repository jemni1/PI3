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
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class RecyclageController extends AbstractController
{
    private UnsplashService $unsplashService;

    // Inject UnsplashService in the constructor
    public function __construct(UnsplashService $unsplashService)
    {
        $this->unsplashService = $unsplashService;
    }
    #[Route('/admin/recyclage', name: 'admin_recyclage_list')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quantiteFilter = $request->query->get('quantite');
        $energieFilter = $request->query->get('energie');
        $dateFilter = $request->query->get('date');
        $typeFilter = $request->query->get('type');
        $collecteDateFilter = $request->query->get('collecteDate');
        $sort = $request->query->get('sort', 'dateDebut');
        $order = $request->query->get('order', 'desc');
    
        // Requête pour récupérer les recyclages avec leurs collectes
        $qb = $entityManager->getRepository(RecyclageDechet::class)->createQueryBuilder('r')
            ->leftJoin('r.collectes', 'c')
            ->addSelect('c');
    
        if ($quantiteFilter === "highQuantity") {
            $qb->andWhere('r.quantiteRecyclee >= :qMin')->setParameter('qMin', 50);
        } elseif ($quantiteFilter === "lowQuantity") {
            $qb->andWhere('r.quantiteRecyclee < :qMax')->setParameter('qMax', 50);
        }
    
        if ($energieFilter === "highEnergy") {
            $qb->andWhere('r.energieProduite >= :eMin')->setParameter('eMin', 100);
        } elseif ($energieFilter === "lowEnergy") {
            $qb->andWhere('r.energieProduite < :eMax')->setParameter('eMax', 100);
        }
    
        if ($dateFilter) {
            $qb->andWhere('r.dateDebut = :date')->setParameter('date', $dateFilter);
        }
    
        if ($typeFilter) {
            $qb->andWhere('c.typeDechet = :type')->setParameter('type', $typeFilter);
        }
    
        if ($collecteDateFilter) {
            $qb->andWhere('c.dateDebut = :collecteDate')->setParameter('collecteDate', $collecteDateFilter);
        }
    
        // Gestion du tri
        if (in_array($sort, ['quantiteRecyclee', 'energieProduite', 'dateDebut'])) {
            $qb->orderBy('r.' . $sort, $order);
        }
    
        $recyclages = $qb->getQuery()->getResult();
    
        // Récupération séparée des collectes
        $collectes = $entityManager->getRepository(CollecteDechet::class)->findAll();
    
        // Calcul des totaux
        $totalRecyclage = array_sum(array_map(fn($r) => $r->getQuantiteRecyclee(), $recyclages));
        $totalEnergie = array_sum(array_map(fn($r) => $r->getEnergieProduite(), $recyclages));
        $totalCollecte = array_sum(array_map(fn($c) => $c->getQuantite(), $collectes));
    
        return $this->render('recyclage/index.html.twig', [
            'recyclages' => $recyclages,
            'collectes' => $collectes, // 🔥 Ajout des collectes ici
            'totalRecyclage' => $totalRecyclage,
            'totalEnergie' => $totalEnergie,
           'totalQuantite' => $totalRecyclage, // ou $totalCollecte selon ce que tu veux afficher

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
               
                $randomImage = $this->unsplashService->getRandomImage('recyclage');
                $recyclageDechet->setImageUrl($randomImage);  

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
