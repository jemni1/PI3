<?php

namespace App\Controller;
use App\Form\ReponseType;
use App\Entity\Reclamation; // Add this line to import the Reclamation entity
use App\Entity\Reponse;
use App\Repository\ReponseRepository;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
final class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }
    /*#[Route('/afficherReclamation', name: 'afficherReclamation')]
    public function afficherReclamation(ReclamationRepository $repo): Response
    {
        $resul= $repo->findAll();
        return $this->render('reclamation/affichageRec.html.twig', [
            'rec' => $resul
        ]);
    }*/
    #[Route('/afficherReclamation', name: 'afficherReclamation')]
    public function afficherReclamation(ReclamationRepository $repo, ReponseRepository $reponseRepo): Response
    {
        // Fetch all reclamations
        $reclamations = $repo->findAll();
    
        // Fetch the responses for each reclamation
        foreach ($reclamations as $reclamation) {
            $response = $reponseRepo->findOneBy(['idreclamation' => $reclamation]);
            $reclamation->response = $response;
        }
    
        return $this->render('reclamation/affichageRec.html.twig', [
            'rec' => $reclamations,
        ]);
    }
    
    #[Route('/rmRec/{id}', name: 'rmRec')]
    public function rm(ReclamationRepository $repo, ManagerRegistry $mr, int $id): Response
    {
        $rec = $repo->find($id);
        
        if (!$rec) {
            return new Response('Réclamation non trouvé');
        }

        $entityManager = $mr->getManager();

      

    
        $entityManager->remove($rec);
        $entityManager->flush();

        return $this->redirectToRoute('afficherReclamation');
    }

    #[Route('/reponse/{id}', name: 'ajouter_reponse')]
    public function ajouterReponse(
        int $id,
        Request $request,
        ManagerRegistry $mr
    ): Response {
        // Find the Reclamation by ID
        $reclamation = $mr->getRepository(Reclamation::class)->find($id);
        
        if (!$reclamation) {
            return new Response('Réclamation non trouvée');
        }

        // Create a new Reponse entity
        $reponse = new Reponse();

        // Create a form for the response
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the related reclamation for the response
            $reponse->setIdreclamation($reclamation);
            $reponse->setDaterep(new \DateTime()); // Set the current date for the response

            // Persist the new response entity
            $entityManager = $mr->getManager();
            $entityManager->persist($reponse);
            $reclamation->setStatus('Répondu');
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Add a flash message to confirm success
            $this->addFlash('success', 'Réponse ajoutée avec succès !');

            // Redirect back to the list of reclamations or another page
            return $this->redirectToRoute('afficherReclamation');
        }

        // Render the form to add the response
        return $this->render('reclamation/ajouter_reponse.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,  // Pass the reclamation to the view
        ]);
    }

    #[Route('/reponse/update/{id}', name: 'update_reponse')]
    public function updateReponse(
        int $id,
        Request $request,
        ManagerRegistry $mr,
        ReponseRepository $reponseRepo
    ): Response {
        // Charger la réponse à mettre à jour à partir de son ID
        $reponse = $reponseRepo->find($id);

        if (!$reponse) {
            // Si la réponse n'existe pas, retourner une erreur
            $this->addFlash('error', 'Réponse non trouvée');
            return $this->redirectToRoute('afficherReclamation');
        }
         // Créer un formulaire avec les données actuelles de la réponse
         $form = $this->createForm(ReponseType::class, $reponse);
         $form->handleRequest($request);

 
         if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setDaterep(new \DateTime()); // Set the current date for the response

             // Sauvegarder les modifications dans la base de données
             $entityManager = $mr->getManager();
             $entityManager->flush();
 
             // Message de succès
             $this->addFlash('success', 'Réponse mise à jour avec succès');
 
             // Rediriger vers une autre page après l'update (par exemple, la liste des réclamations)
             return $this->redirectToRoute('afficherReclamation');
         }
          // Afficher le formulaire de mise à jour
        return $this->render('reclamation/update_reponse.html.twig', [
            'form' => $form->createView(),
            'reponse' => $reponse,  // Passer la réponse à la vue
        ]);
    }

    #[Route('/reclamation/ajouterreclamation', name: 'ajouter_reclamation')]
    public function ajouterReclamation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation(); // Création d'une nouvelle instance
        $form = $this->createForm(ReclamationType::class, $reclamation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Réclamation ajoutée avec succès !');
            return $this->redirectToRoute('afficherReclamationfront');
        }

        return $this->render('reclamation/ajouterreclamation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/afficherReclamationfront', name: 'afficherReclamationfront')]
    public function afficherReclamationfront(ReclamationRepository $repo, ReponseRepository $reponseRepo): Response
    {
        // Fetch all reclamations
        $reclamations = $repo->findAll();
    
        // Fetch the responses for each reclamation
        foreach ($reclamations as $reclamation) {
            $response = $reponseRepo->findOneBy(['idreclamation' => $reclamation]);
            $reclamation->response = $response;
        }
    
        return $this->render('reclamation/affichageRecfront.html.twig', [
            'rec' => $reclamations,
        ]);
    }

    #[Route('/reclamation/supprimer/{id}', name: 'supprimer_reclamation')]
public function supprimerReclamation(
    int $id,
    ManagerRegistry $mr
): Response {
    // Trouver la réclamation par ID
    $reclamation = $mr->getRepository(Reclamation::class)->find($id);

    // Vérifier si la réclamation existe
    if (!$reclamation) {
        // Si la réclamation n'existe pas, afficher un message d'erreur
        $this->addFlash('error', 'Réclamation non trouvée.');
        return $this->redirectToRoute('afficherReclamation');
    }

    // Récupérer l'Entity Manager
    $entityManager = $mr->getManager();

    // Supprimer la réclamation
    $entityManager->remove($reclamation);
    $entityManager->flush();

    // Ajouter un message flash de succès
    $this->addFlash('success', 'Réclamation supprimée avec succès.');

    // Rediriger vers la liste des réclamations
    return $this->redirectToRoute('afficherReclamationfront');
}
#[Route('/reclamation/{id}/editreclamation', name: 'edit_reclamation')]
    public function editReclamation(
        int $id, 
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response {
        // Trouver la réclamation par son ID
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Créer le formulaire de mise à jour et le lier à l'entité Reclamation
        $form = $this->createForm(ReclamationType::class, $reclamation);

        // Gérer la requête
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour la réclamation dans la base de données
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Réclamation mise à jour avec succès');

            // Rediriger vers une autre page (par exemple, la liste des réclamations)
            return $this->redirectToRoute('afficherReclamationfront');
        }

        // Rendu du formulaire pour la mise à jour
        return $this->render('reclamation/editreclamation.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }


}
