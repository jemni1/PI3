<?php

namespace App\Controller;

use App\Entity\RecyclageDechet;
use App\Entity\CollecteDechet;
use App\Form\RecyclageDechetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RecyclageController extends AbstractController
{
    #[Route('/admin/recyclage', name: 'admin_recyclage_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $recyclages = $entityManager->getRepository(RecyclageDechet::class)->findAll();
        $collectes = $entityManager->getRepository(CollecteDechet::class)->findAll(); // Ajout des collectes

        return $this->render('recyclage/index.html.twig', [
            'recyclages' => $recyclages,
            'collectes' => $collectes, // Passer les collectes à la vue
        ]);
    }

    #[Route('/admin/recyclage/new', name: 'admin_recyclage_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recyclageDechet = new RecyclageDechet();
        $form = $this->createForm(RecyclageDechetType::class, $recyclageDechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recyclageDechet);
            $entityManager->flush();

            $this->addFlash('success', 'Recyclage ajouté avec succès !');
            return $this->redirectToRoute('admin_recyclage_list');
        }

        return $this->render('recyclage/recyclage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/recyclage/edit/{id}', name: 'admin_recyclage_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, RecyclageDechet $recyclageDechet): Response
    {
        $form = $this->createForm(RecyclageDechetType::class, $recyclageDechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Recyclage modifié avec succès !');
            return $this->redirectToRoute('admin_recyclage_list');
        }

        return $this->render('recyclage/recyclage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/recyclage/delete/{id}', name: 'admin_recyclage_delete')]
    public function delete(EntityManagerInterface $entityManager, RecyclageDechet $recyclageDechet): Response
    {
        $entityManager->remove($recyclageDechet);
        $entityManager->flush();

        $this->addFlash('success', 'Recyclage supprimé avec succès !');
        return $this->redirectToRoute('admin_recyclage_list');
    }
}
