<?php
namespace App\Controller;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commandes;
use App\Entity\Produits;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function payment(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('view_cart');
        }
        
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['quantite'] * $item['prix'];
        }
        
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        
        $paymentIntent = PaymentIntent::create([
            'amount' => $totalAmount * 100,
            'currency' => 'eur',
        ]);
        
        return $this->render('payment/payment_form.html.twig', [
            'totalAmount' => $totalAmount,
            'stripePublicKey' => $_ENV['STRIPE_PUBLIC_KEY'],
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    #[Route('/process_payment', name: 'process_payment', methods: ['POST'])]
    public function processPayment(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): JsonResponse
    {
        $cart = $session->get('cart', []);
        
        try {
            $data = json_decode($request->getContent(), true);
            $paymentIntentId = $data['payment_intent_id'];
            
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            if ($paymentIntent->status === 'succeeded') {
                $entityManager->beginTransaction();
                
                foreach ($cart as $id => $item) {
                    $produit = $entityManager->getRepository(Produits::class)->find($item['id_produit']);
                    
                    $commande = new Commandes();
                    $commande->setId_produit($produit->getId());
                    $commande->setNom($produit->getNom());
                    $commande->setQuantite($item['quantite']);
                    $commande->setPrix($item['quantite'] * $produit->getPrix());
                    $commande->setStatut('Payée');
                    $commande->setDate(new \DateTime());
                    $commande->setId_client(1);
                    
                    $produit->setQuantite($produit->getQuantite() - $item['quantite']);
                    $entityManager->persist($commande);
                    $entityManager->persist($produit);
                }
                
                $entityManager->flush();
                $entityManager->commit();
                
                $session->remove('cart');
                
                return new JsonResponse(['status' => 'success']);
            }
            
            return new JsonResponse(['status' => 'error', 'message' => 'Paiement non réussi'], 400);
            
        } catch (\Exception $e) {
            $entityManager->rollback();
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}