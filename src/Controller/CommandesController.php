<?php

namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandesRepository;
use App\Repository\ProduitsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Commandes;
use App\Entity\Produits;
use Doctrine\ORM\EntityManagerInterface;
final class CommandesController extends AbstractController
{
    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {
        $produit = $entityManager->getRepository(Produits::class)->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }
    
        // Récupérer la quantité saisie
        $quantity = $request->request->get('quantity', 1); 
        $quantity = (int)$quantity;
    
        // Vérification de la quantité
        if ($quantity < 1) {
            $this->addFlash('error', 'La quantité doit être supérieure ou égale à 1.');
            return $this->redirectToRoute('listprod', [
                'id' => $id,
            ]);
        }
    
        if ($quantity > $produit->getQuantite()) {
            $this->addFlash('error', 'La quantité demandée dépasse le stock disponible.');
            return $this->redirectToRoute('listprod', [
                'id' => $id,
            ]);
        }
    
        // Récupérer le panier actuel dans la session (ou créer un nouveau)
        $cart = $session->get('cart', []);
    
        // Vérifier si le produit est déjà dans le panier
        if (isset($cart[$id])) {
            $cart[$id]['quantite'] += $quantity; // Ajouter la quantité spécifiée
        } else {
            $cart[$id] = [
                'id_produit' => $produit->getId(),
                'nom' => $produit->getNom(),
                'quantite' => $quantity,
                'prix' => $produit->getPrix(),
            ];
        }
    
        // Enregistrer le panier dans la session
        $session->set('cart', $cart);
    
        $this->addFlash('success', 'Produit ajouté au panier avec succès !');
    
        return $this->redirectToRoute('listprod');
    }
    

#[Route('/cart', name: 'view_cart')]
public function viewCart(SessionInterface $session): Response
{
    // Récupérer le panier de la session
    $cart = $session->get('cart', []);

    // Calculer le total
    $total = array_reduce($cart, function ($carry, $item) {
        return $carry + ($item['prix'] * $item['quantite']);
    }, 0);

    return $this->render('commandes/index.html.twig', [
        'cart' => $cart,
        'total' => $total,
    ]);
}

#[Route('/cart/update/{id}', name: 'update_cart', methods: ['POST'])]
public function updateCart(int $id, Request $request, SessionInterface $session, EntityManagerInterface $entityManager,): Response
{   
    $produit = $entityManager->getRepository(Produits::class)->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }
    $cart = $session->get('cart', []);

    if (isset($cart[$id])) {
        $quantity = (int)$request->request->get('quantity');
        $quantity = max(1, $quantity); // Assurez que la quantité est au moins 1
        if ($quantity < 1) {
            $this->addFlash('error', 'La quantité doit être supérieure ou égale à 1.');
            return $this->redirectToRoute('view_cart', [
                'id' => $id,
            ]);
        }
    
        if ($quantity > $produit->getQuantite()) {
            $this->addFlash('error', 'La quantité demandée dépasse le stock disponible.');
            return $this->redirectToRoute('view_cart', [
                'id' => $id,
            ]);
        }
        $cart[$id]['quantite'] = $quantity;
        $session->set('cart', $cart);

        $this->addFlash('success', 'La quantité a été mise à jour.');
    } else {
        $this->addFlash('error', 'Produit non trouvé dans le panier.');
    }

    return $this->redirectToRoute('view_cart');
}

#[Route('/cart/remove/{id}', name: 'remove_from_cart')]
public function removeFromCart(int $id, SessionInterface $session): Response
{
    $cart = $session->get('cart', []);

    if (isset($cart[$id])) {
        unset($cart[$id]);
        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit supprimé du panier.');
    } else {
        $this->addFlash('error', 'Produit non trouvé dans le panier.');
    }

    return $this->redirectToRoute('view_cart');
}

#[Route('/checkout', name: 'checkout', methods: ['POST'])]
public function checkout(SessionInterface $session, EntityManagerInterface $entityManager): Response
{
    // Récupérer le panier depuis la session
    $cart = $session->get('cart', []);

    // Débogage : vérifier le contenu du panier
    dump($cart);

    // Vérifier si le panier est vide
    if (empty($cart)) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('view_cart');
    }

    try {
        // Démarrer une transaction
        $entityManager->beginTransaction();

        foreach ($cart as $id => $item) {
            // Vérifier que toutes les données nécessaires sont présentes
            if (!isset($item['id_produit']) || !isset($item['quantite'])) {
                throw new \Exception('Données du panier invalides');
            }

            // Récupérer le produit depuis la base de données
            $produit = $entityManager->getRepository(Produits::class)->find($item['id_produit']);
            
            if (!$produit) {
                throw new \Exception('Produit introuvable : ' . $item['id_produit']);
            }

            // Vérifier si la quantité demandée est disponible
            if ($item['quantite'] > $produit->getQuantite()) {
                throw new \Exception('Stock insuffisant pour ' . $produit->getNom());
            }

            // Créer une nouvelle commande
            $commande = new Commandes();
            $commande->setId_produit($produit->getId());
            $commande->setNom($produit->getNom());
            $commande->setQuantite($item['quantite']);
            $commande->setPrix($item['quantite'] * $produit->getPrix());
            $commande->setStatut('En cours');
            $commande->setDate(new \DateTime());
            $commande->setId_client(1); // À remplacer par l'ID du client connecté

            // Mettre à jour le stock du produit
            $nouvelles_quantite = $produit->getQuantite() - $item['quantite'];
            $produit->setQuantite($nouvelles_quantite);

            // Persister les entités
            $entityManager->persist($commande);
            $entityManager->persist($produit);
            
            // Débogage : vérifier les entités avant flush
            dump($commande);
            dump($produit);
        }

        // Sauvegarder toutes les modifications
        $entityManager->flush();
        
        // Si tout s'est bien passé, on valide la transaction
        $entityManager->commit();

        // Vider le panier
        $session->remove('cart');

        $this->addFlash('success', 'Votre commande a été passée avec succès.');
        return $this->redirectToRoute('listprod');

    } catch (\Exception $e) {
        // En cas d'erreur, on annule la transaction
        $entityManager->rollback();
        
        // Log l'erreur
        error_log($e->getMessage());
        
        $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        return $this->redirectToRoute('view_cart');
    }
}
#[Route('/admin/commandes/{terrainId}', name: 'admin_commandes_by_terrain')]
public function index(int $terrainId, CommandesRepository $commandesRepository, ProduitsRepository $produitsRepository): Response
{
    // Fetch all products that belong to the specified terrain
    $produits = $produitsRepository->findBy(['id_terrain' => $terrainId]);
    
    // Get the IDs of the products associated with the terrain
    $productIds = array_map(fn($produit) => $produit->getId(), $produits);

    // Fetch orders that have products related to the selected terrain
    $commandes = $commandesRepository->findBy(['id_produit' => $productIds]);

    return $this->render('commandes/admin.html.twig', [
        'commandes' => $commandes,
    ]);
}
#[Route('/user/commandes/{userId}', name: 'user_commandes_by_terrain')]
public function indexuser(int $userId, CommandesRepository $commandesRepository, ProduitsRepository $produitsRepository): Response
{
 
    // Fetch orders that have products related to the selected terrain
    $commandes = $commandesRepository->findBy(['id_client' => $userId]);

    return $this->render('commandes/user.html.twig', [
        'commandes' => $commandes,
    ]);
}

}