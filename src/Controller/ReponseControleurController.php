<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReponseControleurController extends AbstractController
{
    #[Route('/reponse/controleur', name: 'app_reponse_controleur')]
    public function index(): Response
    {
        return $this->render('reponse_controleur/index.html.twig', [
            'controller_name' => 'ReponseControleurController',
        ]);
    }
    
    
    
    
    /*#[Route('/reponse/{id}', name: 'app_ajouter_reponse')]
    public function ajouterReponse(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Find the Reclamation by ID
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);
        if (!$reclamation) {
            // If no reclamation is found, return an error response
            return $this->createNotFoundException('Reclamation not found');
        }

        // Create a new response
        $reponse = new Reponse();
        $form = $this->createFormBuilder($reponse)
            ->add('message', TextType::class, [
                'label' => 'Response Message',
                'attr' => ['placeholder' => 'Enter your response'],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Add Response'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Automatically set the current date for the response
            $reponse->setDaterep(); // This uses the default setter logic for current date
            $reponse->set($reclamation); // Link the response to the reclamation

            // Persist the new response
            $entityManager->persist($reponse);
            $entityManager->flush();

            // Redirect or render a success message
            return $this->redirectToRoute('app_reponse_controleur');
        }

        return $this->render('reponse/ajouter_reponse.html.twig', [
            'form' => $form->createView(),
        ]);
    }


#[Route('/reponse/{id}', name: 'app_add_reponse')]
    public function addReponse(
        int $id,
        Request $request,  // Add the correct use statement at the top
        EntityManagerInterface $entityManager
    ): Response {
        // Find the reclamation by ID
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        // Create a new Reponse instance
        $reponse = new Reponse();

        // Create the form for the response
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the related reclamation for the response
            $reponse->setIdreclamation($reclamation);
            $reponse->setDaterep(new \DateTime()); // Set the current date for the response

            // Persist the new response object
            $entityManager->persist($reponse);
            $entityManager->flush();

            // Add a flash message to confirm success
            $this->addFlash('success', 'Response added successfully!');

            // Redirect to the list of reclamations (or wherever appropriate)
            return $this->redirectToRoute('app_reclamations');
        }

        // Render the template and pass the form and reclamation to the view
        return $this->render('reponse/ajouter_reponse.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }


*/





}
