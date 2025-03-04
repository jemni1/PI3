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
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function allProducts(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        Request $request,
        TerrainsRepository $terrainsRepository,
        PaginatorInterface $paginator
    ): Response {
        $session = $requestStack->getSession();
        $terrainId = $session->get('terrain_id', null);
        $terrain = $terrainsRepository->find($terrainId);
    
        if (!$terrain) {
            return $this->json(['error' => 'Terrain non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // Get the filter parameters (name and price)
        $productName = $request->query->get('product_name', null);
        $minPrice = $request->query->get('min_price', null);
        $maxPrice = $request->query->get('max_price', null);

        $page = $request->query->getInt('page', 1);
        $limit = 6; // Unifier la limite avec la pagination
    
        $produitsRepository = $entityManager->getRepository(Produits::class);
    
    $produitsQuery = $produitsRepository->createQueryBuilder('p')
        ->where('p.id_terrain = :terrainId')
        ->setParameter('terrainId', $terrain);

    // Apply filters
    if ($productName) {
        $produitsQuery->andWhere('p.nom LIKE :productName')
            ->setParameter('productName', '%' . $productName . '%');
    }
    if ($minPrice) {
        $produitsQuery->andWhere('p.prix >= :minPrice')
            ->setParameter('minPrice', $minPrice);
    }
    if ($maxPrice) {
        $produitsQuery->andWhere('p.prix <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice);
    }
        $pagination = $paginator->paginate(
            $produitsQuery, // Passer la requête et non un tableau
            $page, 
            $limit
        );
    
        return $this->render('products/list.html.twig', [
            'produits' => $pagination,
            'product_name' => $productName,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
        ]);
    }
    
    #[Route('/ajax/product_search', name: 'ajax_product_search')]
public function ajaxProductSearch(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $query = $request->query->get('query');
    
    if (!$query) {
        return new JsonResponse(['suggestions' => []]);
    }

    // Get products matching the query (filter by name)
    $produitsRepository = $entityManager->getRepository(Produits::class);
    $produits = $produitsRepository->createQueryBuilder('p')
        ->where('p.nom LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->setMaxResults(5) // Limit results to 5 suggestions
        ->getQuery()
        ->getResult();

    $suggestions = [];

    foreach ($produits as $produit) {
        $suggestions[] = [
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
        ];
    }

    return new JsonResponse(['suggestions' => $suggestions]);
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
#[Route('/viewuser/{id}', name: 'produit_viewuser')]
public function viewuser(int $id, ProduitsRepository $produitsRepository): Response
{
    $produit = $produitsRepository->find($id);

    if (!$produit) {
        throw $this->createNotFoundException('Le produit n\'existe pas.');
    }

    return $this->render('products/viewuser.html.twig', [
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
public function list(
    ProduitsRepository $productRepository,
    SessionInterface $session,
    PaginatorInterface $paginator,
    Request $request
): Response {
    // Get the filter parameters (name and price)
    $productName = $request->query->get('product_name', null);
    $minPrice = $request->query->get('min_price', null);
    $maxPrice = $request->query->get('max_price', null);

    $page = $request->query->getInt('page', 1);
    $limit = 6; // Pagination limit

    // Get the query builder
    $query = $productRepository->createQueryBuilder('p');

    // Apply filters if any
    if ($productName) {
        $query->andWhere('p.nom LIKE :productName')
            ->setParameter('productName', '%' . $productName . '%');
    }
    if ($minPrice) {
        $query->andWhere('p.prix >= :minPrice')
            ->setParameter('minPrice', $minPrice);
    }
    if ($maxPrice) {
        $query->andWhere('p.prix <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice);
    }

    // Paginate the filtered products
    $pagination = $paginator->paginate(
        $query, // Pass the query builder for pagination
        $page, 
        $limit
    );

    // Render the view with filtered products
    return $this->render('products/listuser.html.twig', [
        'pagination' => $pagination,
        'product_name' => $productName,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
    ]);
}



#[Route('/rechercher', name: 'search_products')]
public function searchProducts(
    Request $request,
    ProduitsRepository $produitsRepository,
    PaginatorInterface $paginator
): Response {
    $productName = $request->query->get('q');  // Paramètre de recherche
    $minPrice = $request->query->get('min_price');
    $maxPrice = $request->query->get('max_price');

    $produitsQuery = $produitsRepository->createQueryBuilder('p')
        ->where('p.nom LIKE :productName')
        ->setParameter('productName', '%' . $productName . '%');

    if ($minPrice) {
        $produitsQuery->andWhere('p.prix >= :minPrice')
            ->setParameter('minPrice', $minPrice);
    }

    if ($maxPrice) {
        $produitsQuery->andWhere('p.prix <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice);
    }

    $produitsQuery = $produitsQuery->getQuery();
    $page = $request->query->getInt('page', 1);
    $limit = 6;
    $pagination = $paginator->paginate(
        $produitsQuery,
        $page,
        $limit
    );

    return $this->render('products/listuser.html.twig', [
        'pagination' => $pagination,
        'product_name' => $productName,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
    ]);
}





#[Route('/rechercher/ajax', name: 'search_ajax_products')]
public function searchAjax(ProduitsRepository $productRepository, Request $request): JsonResponse
{
    $searchTerm = $request->query->get('q');

    // Si le terme est vide, retourner un tableau vide
    if (!$searchTerm) {
        return new JsonResponse([]);
    }

    // Filtrer les produits selon le terme de recherche
    $products = $productRepository->findByName($searchTerm);

    // Créer un tableau avec les données des produits pour la réponse JSON
    $result = [];
    foreach ($products as $product) {
        $result[] = [
            'id' => $product->getId(),
            'nom' => $product->getNom(),
        ];
    }

    // Retourner les produits en JSON
    return new JsonResponse($result);
}


}
    

