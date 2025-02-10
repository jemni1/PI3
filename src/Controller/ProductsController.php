<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Form\ProduiteditType;
use App\Repository\ProduitsRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
                        $this->getParameter('images_directory'), // Paramètre de dossier
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
public function allProducts(EntityManagerInterface $entityManager): Response
{
    // Récupérer tous les produits depuis la base de données
    $produits = $entityManager->getRepository(Produits::class)->findAll();

    // Passer les produits à la vue
    return $this->render('products/list.html.twig', [
        'produits' => $produits,
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
public function list(ProduitsRepository $productRepository): Response
{
    $products = $productRepository->findAvailableProducts();


    return $this->render('products/listuser.html.twig', [
        'products' => $products
    ]);
}

}
    

