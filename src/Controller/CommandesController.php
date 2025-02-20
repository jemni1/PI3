<?php

namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandesRepository;
use App\Repository\ProduitsRepository;
use App\EventSubscriber\CartSubscriber;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\TerrainsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Commandes;
use App\Entity\Produits;
use Dompdf\Dompdf;
use Dompdf\Options;
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
    
        $quantity = $request->request->get('quantity', 1); 
        $quantity = (int)$quantity;
    
        
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
    
        $cart = $session->get('cart', []);
    
        if (isset($cart[$id])) {
            $cart[$id]['quantite'] += $quantity; 
        } else {
            $cart[$id] = [
                'id_produit' => $produit->getId(),
                'nom' => $produit->getNom(),
                'quantite' => $quantity,
                'prix' => $produit->getPrix(),
            ];
        }
    
        $session->set('cart', $cart);
    
        $this->addFlash('success', 'Produit ajouté au panier avec succès !');
    
        return $this->redirectToRoute('listprod');
    }
    

    #[Route('/cart', name: 'view_cart')]
    public function viewCart(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        $total = array_reduce($cart, function ($carry, $item) {
            if (!is_array($item) || !isset($item['prix']) || !isset($item['quantite'])) {
                return $carry; 
            }
            return $carry + ($item['prix'] * $item['quantite']);
        }, 0);
        
        $cart = array_filter($cart, function($item) {
            return is_array($item) && isset($item['prix']) && isset($item['quantite']);
        });
        
        $session->set('cart', $cart);
        
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
        $quantity = max(1, $quantity); 
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
    $cart = $session->get('cart', []);

    dump($cart);

    if (empty($cart)) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('view_cart');
    }

    try {
        $entityManager->beginTransaction();

        foreach ($cart as $id => $item) {
            if (!isset($item['id_produit']) || !isset($item['quantite'])) {
                throw new \Exception('Données du panier invalides');
            }

            $produit = $entityManager->getRepository(Produits::class)->find($item['id_produit']);
            
            if (!$produit) {
                throw new \Exception('Produit introuvable : ' . $item['id_produit']);
            }

            if ($item['quantite'] > $produit->getQuantite()) {
                throw new \Exception('Stock insuffisant pour ' . $produit->getNom());
            }

            $commande = new Commandes();
            $commande->setId_produit($produit->getId());
            $commande->setNom($produit->getNom());
            $commande->setQuantite($item['quantite']);
            $commande->setPrix($item['quantite'] * $produit->getPrix());
            $commande->setStatut('En cours');
            $commande->setDate(new \DateTime());
            $commande->setId_client(1);

            $nouvelles_quantite = $produit->getQuantite() - $item['quantite'];
            $produit->setQuantite($nouvelles_quantite);

            $entityManager->persist($commande);
            $entityManager->persist($produit);
            
            dump($commande);
            dump($produit);
        }

        $entityManager->flush();
        
        $entityManager->commit();

        $session->remove('cart');

        $this->addFlash('success', 'Votre commande a été passée avec succès.');
        return $this->redirectToRoute('listprod');

    } catch (\Exception $e) {
        $entityManager->rollback();
        
        error_log($e->getMessage());
        
        $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        return $this->redirectToRoute('view_cart');
    }
}
#[Route('/admin/commandes', name: 'admin_commandes_by_terrain')]
public function index(TerrainsRepository $terrainsRepository, RequestStack $requestStack,CommandesRepository $commandesRepository, ProduitsRepository $produitsRepository): Response
{
    $session = $requestStack->getSession();
    $terrainId = $session->get('terrain_id', null);
    $terrain = $terrainsRepository->find($terrainId);
        if (!$terrain) {
            return $this->json(['error' => 'Terrain non trouvé'], Response::HTTP_NOT_FOUND);
        }
    $produits = $produitsRepository->findBy(['id_terrain' => $terrain]);
    
    $productIds = array_map(fn($produit) => $produit->getId(), $produits);

    $commandes = $commandesRepository->findBy(['id_produit' => $productIds]);

    return $this->render('commandes/admin.html.twig', [
        'commandes' => $commandes,
    ]);
}
#[Route('/user/commandes', name: 'user_commandes_by_terrain')]
public function indexuser( CommandesRepository $commandesRepository, ProduitsRepository $produitsRepository): Response
{
 
    // $commandes = $commandesRepository->findBy(['id_client' => $userId]);
    $commandes = $commandesRepository->findAll();

    return $this->render('commandes/user.html.twig', [
        'commandes' => $commandes,
    ]);
}
// #[Route('/user/commandes/{userId}', name: 'user_commandes_by_terrain')]
// public function indexuser(int $userId, CommandesRepository $commandesRepository, ProduitsRepository $produitsRepository): Response
// {
 
//     // Fetch orders that have products related to the selected terrain
//     $commandes = $commandesRepository->findBy(['id_client' => $userId]);

//     return $this->render('commandes/user.html.twig', [
//         'commandes' => $commandes,
//     ]);
// }

#[Route('/factureadmin/{id}', name:"facture_admin")]
public function indexcommm(int $id, CommandesRepository $commandesRepository, ProduitsRepository $produitsRepository): Response
{
    $commande = $commandesRepository->find($id);
    
    if (!$commande) {
        throw $this->createNotFoundException('La commande n\'existe pas.');
    }
    $idProduit = $commande->getId_produit();
    $produit = $produitsRepository->find($idProduit);

    $idTerrain = $produit->getIdTerrain()->getId();

    // $produits = $produitsRepository->findByCommande($commande);

    return $this->render('commandes/admincmd.html.twig', [
        'commande' => $commande, 
        'idTerrain' => $idTerrain,
    ]);
}
#[Route('/facture/{id}', name:"facture_show")]
public function indexcomm(int $id, CommandesRepository $commandesRepository): Response
{
    $commande = $commandesRepository->find($id);
    
    if (!$commande) {
        throw $this->createNotFoundException('La commande n\'existe pas.');
    }

    // $produits = $produitsRepository->findByCommande($commande);

    return $this->render('commandes/usercmd.html.twig', [
        'commande' => $commande,  // Utiliser 'commande' ici au lieu de 'commandes'
    ]);
}
#[Route('/facture/delete/{id}', name:"facture_delete")]
public function indexdelete(int $id, CommandesRepository $commandesRepository,EntityManagerInterface $entityManager, Request $request): Response
{
    $commande = $commandesRepository->find($id);

    if (!$commande) {
        throw $this->createNotFoundException('La commande n\'existe pas.');
    }
    $clientId = $commande->getId_client();
    $entityManager->remove($commande);
    $entityManager->flush();
    $this->addFlash('success', 'Commande supprimée avec succès.');

    return $this->redirectToRoute('user_commandes_by_terrain', ['userId' => $clientId]);
}



  /**
     * @Route("/commande/{commandeId}/facture", name="generate_invoice_pdf")
     */
    public function generateInvoicePdf($commandeId, EntityManagerInterface $entityManager): Response
    {
        $commande = $entityManager->getRepository(Commandes::class)->find($commandeId);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $dompdf = new Dompdf();

        $html = $this->renderView('commandes/pdf.html.twig', [
            'commande' => $commande
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="facture_' . $commande->getId() . '.pdf"'
            ]
        );
    }
    /**
     * @Route("/commandeAdmin/{commandeId}/facture", name="generate_invoice_pdf")
     */
    public function generateInvoicePdfAdmin($commandeId, EntityManagerInterface $entityManager): Response
    {
        $commande = $entityManager->getRepository(Commandes::class)->find($commandeId);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $dompdf = new Dompdf();

        $html = $this->renderView('commandes/pdfadmin.html.twig', [
            'commande' => $commande
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="facture_' . $commande->getId() . '.pdf"'
            ]
        );
    }
}


