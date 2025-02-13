<?php
namespace App\Controller;

use App\Entity\CollecteDechet;
use App\Form\CollecteDechetType;
use App\Repository\CollecteDechetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CollecteDechetController extends AbstractController
{
    // ✅ Affichage et ajout d'une collecte par l'employé
    #[Route('/collecte', name: 'collecte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CollecteDechetRepository $collecteRepo): Response
    {
        $collecte = new CollecteDechet();
        $form = $this->createForm(CollecteDechetType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collecte);
            $entityManager->flush();

            $this->addFlash('success', 'Collecte enregistrée avec succès !');
            return $this->redirectToRoute('collecte_new');
        }

        return $this->render('collecte/index.html.twig', [
            'form' => $form->createView(),
            'collectes' => $collecteRepo->findAll(),
        ]);
    }

    // ✅ Modification d'une collecte
    #[Route('/collecte/{id}/edit', name: 'collecte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CollecteDechet $collecte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollecteDechetType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Collecte modifiée avec succès !');

            return $this->redirectToRoute('collecte_new');
        }

        return $this->render('collecte/edit.html.twig', [
            'form' => $form->createView(),
            'collecte' => $collecte,
        ]);
    }

    // ✅ Suppression d'une collecte
    #[Route('/collecte/{id}/delete', name: 'collecte_delete', methods: ['POST'])]
    public function delete(Request $request, CollecteDechet $collecte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $collecte->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collecte);
            $entityManager->flush();

            $this->addFlash('success', 'Collecte supprimée avec succès !');
        }

        return $this->redirectToRoute('collecte_new');
    }
}
