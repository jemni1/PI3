<?php

namespace App\Controller;
use App\Repository\EquipementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Equipement;  // 
use App\Form\EquipementType;
use Symfony\Component\HttpFoundation\Response;

final class EquipementController extends AbstractController
{
    #[Route('/equipement', name: 'app_equipement')]
    public function index(): Response
    {
        return $this->render('equipement/index.html.twig', [
            'controller_name' => 'EquipementController',
        ]);
    }

    #[Route('/AfficheEquipement', name: 'equipement_list')]
    public function AfficheEquipements(EquipementRepository $equipementRepository): Response
    {
        $equipements = $equipementRepository->findAll();

        return $this->render('equipement/AfficheEquipement.html.twig', [
            'equipements' => $equipements
        ]);
    }

   
    #[Route('/ajouterEquipement', name: 'equipement_add')]
    public function ajouterEquipements(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une instance vide de l'entité Equipement
        $equipement = new Equipement();
    
        // Créer le formulaire
        $form = $this->createForm(EquipementType::class, $equipement);
        
        // Gérer la requête
        // request(recuperation des donnees du formilaire)
        $form->handleRequest($request);
    
        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persister l'entité
            $entityManager->persist($equipement); // entityManager gere les donne  
            $entityManager->flush();
    
            // Redirection après ajout
            return $this->redirectToRoute('equipement_list');
        }
    
        // Affichage du formulaire
        return $this->render('Equipement/AjouterEquipement.html.twig', [
            'EquipementForm' => $form->createView(),
        ]);
    }

#[Route('/delete/{id}', name: 'd', methods: ['GET', 'POST'])]
public function delete(int $id, EquipementRepository $repository, EntityManagerInterface $em): Response
{
    $equipement = $repository->find($id);

    $em->remove($equipement);
    $em->flush();

    return $this->redirectToRoute('equipement_list');


}

#[Route('/modifier/{id}', name: 'equipement_edit', methods: ['GET', 'POST'])]
public function modifierEquipement(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(EquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('equipement_list');
    }

    return $this->render('equipement/modificationEquipement.html.twig', [
        'EquipementForm' => $form->createView(),
    ]);
}



}
