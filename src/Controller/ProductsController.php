<?php

namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Form\ProduiteditType;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductsController extends AbstractController
{
    #[Route('/products', name: 'add_products')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('imagess_directory'), // Paramètre de dossier
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
                $produit->setImage($newFilename);
                
                // $produit->setIdTerrain($id);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès!');

            // Réinitialiser le formulaire en créant un nouvel objet Produits
            return $this->redirectToRoute('app_all_products');
        }

        return $this->render('products/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/all', name: 'app_all_products')]
public function allProducts(EntityManagerInterface $entityManager, Request $request): Response
{
    $page = $request->query->getInt('page', 1); // Page actuelle, par défaut la page 1
    $limit = 8; // Nombre d'éléments par page

    // Récupérer les produits paginés
    $produitsRepository = $entityManager->getRepository(Produits::class);
    $produitsQuery = $produitsRepository->createQueryBuilder('p')
        ->setFirstResult(($page - 1) * $limit) // Offset
        ->setMaxResults($limit); // Limite

    $produits = $produitsQuery->getQuery()->getResult(); // Exécute la requête

    // Récupérer le nombre total de produits pour calculer le nombre de pages
    $totalProduits = $produitsRepository->count([]);
    $totalPages = ceil($totalProduits / $limit); // Calculer le nombre total de pages

    return $this->render('products/list.html.twig', [
        'produits' => $produits,
        'current_page' => $page,
        'total_pages' => $totalPages,
    ]);
}
#[Route('/view/{id}', name: 'produit_view')]
public function view(int $id, ProduitsRepository $produitsRepository): Response
{
    $produit = $produitsRepository->find($id);

    if (!$produit) {
        throw $this->createNotFoundException('Le produit n\'existe pas.');
    }

    return $this->render('products/view.html.twig', [
        'produit' => $produit,
    ]);
}
#[Route('/produit/edit/{id}', name: 'produit_edit')]
public function edit(Produits $produit, Request $request, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ProduiteditType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a été modifié avec succès !');
        return $this->redirectToRoute('app_all_products');
    }

    return $this->render('products/edit.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit
    ]);
}

#[Route('/produits/{id}/supprimer', name: 'produit_delete')]
public function delete(int $id, ProduitsRepository $produitsRepository, EntityManagerInterface $entityManager): RedirectResponse
{
    $produit = $produitsRepository->find($id);

    if ($produit) {
        // Suppression du produit
        $entityManager->remove($produit); // Utilisation de l'EntityManager pour supprimer l'entité
        $entityManager->flush(); // Sauvegarde de la suppression en base de données

        $this->addFlash('success', 'Produit supprimé avec succès!');
    }

    return $this->redirectToRoute('app_all_products'); // Remplacez 'produit_list' par le nom de la route de la liste des produits
}


#[Route('/liste', name: 'listprod')]
public function list(ProduitsRepository $productRepository, SessionInterface $session, PaginatorInterface $paginator, Request $request): Response
{
    $query = $productRepository->findAvailableProducts(); // Utilise une requête pour la pagination

    $pagination = $paginator->paginate(
        $query, // La requête
        $request->query->getInt('page', 1), 
        12 
    );

    return $this->render('products/listuser.html.twig', [
        'pagination' => $pagination,
    ]);
}



}
    

