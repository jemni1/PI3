<?php

namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Form\ProduiteditType;
use App\Repository\ProduitsRepository;
use App\Repository\TerrainsRepository;
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
    public function index(RequestStack $requestStack,Request $request, EntityManagerInterface $entityManager,TerrainsRepository $terrainsRepository): Response
    {   $session = $requestStack->getSession();
        $terrainId = $session->get('terrain_id', null);
        $terrain = $terrainsRepository->find($terrainId);

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
                        $this->getParameter('imagess_directory'), 
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
                $produit->setImage($newFilename);
                $produit->setIdTerrain($terrain);
                
                
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès!');

            return $this->redirectToRoute('app_all_products');
        }

        return $this->render('products/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/all', name: 'app_all_products')]
public function allProducts(RequestStack $requestStack,EntityManagerInterface $entityManager, Request $request,TerrainsRepository $terrainsRepository): Response
{
    $session = $requestStack->getSession();
    $terrainId = $session->get('terrain_id', null);
    $terrain = $terrainsRepository->find($terrainId);
        if (!$terrain) {
            return $this->json(['error' => 'Terrain non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $page = $request->query->getInt('page', 1);
        $limit = 8;
        $offset = ($page - 1) * $limit;

        $produitsRepository = $entityManager->getRepository(Produits::class);

        $produitsQuery = $produitsRepository->createQueryBuilder('p')
            ->where('p.id_terrain = :terrainId')
            ->setParameter('terrainId', $terrain)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $produits = $produitsQuery->getResult();

        $totalProduits = $produitsRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.id_terrain = :terrainId')
            ->setParameter('terrainId', $terrain)
            ->getQuery()
            ->getSingleScalarResult();

        $totalPages = ceil($totalProduits / $limit);

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
        $entityManager->remove($produit); 
        $entityManager->flush(); 

        $this->addFlash('success', 'Produit supprimé avec succès!');
    }

    return $this->redirectToRoute('app_all_products'); 
}


#[Route('/liste', name: 'listprod')]
public function list(ProduitsRepository $productRepository, SessionInterface $session, PaginatorInterface $paginator, Request $request): Response
{
    $query = $productRepository->findAvailableProducts();

    $pagination = $paginator->paginate(
        $query, 
        $request->query->getInt('page', 1), 
        6
    );

    return $this->render('products/listuser.html.twig', [
        'pagination' => $pagination,
    ]);
}



}
    

