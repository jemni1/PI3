<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\CultureType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Culture;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CultureRepository;


final class CultureController extends AbstractController
{
    #[Route('/culture', name: 'app_culture')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $cultures = $doctrine->getRepository(Culture::class)->findAll();

        return $this->render('culture/index.html.twig', [
            'cultures' => $cultures,
        ]);
    }
    #[Route('/culture/new', name: 'new_culture')]
    public function new(Request $req,EntityManagerInterface $mr,ManagerRegistry $doctrine):Response{
        $culture = new Culture();
        $form =$this->createForm(CultureType::class,$culture);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $mr->persist($culture);
            $mr->flush(); 
            return $this->render('culture/index.html.twig',['cultures'=>$doctrine->getRepository(Culture::class)->findAll()]);
            
        }
        return $this->render('culture/newCulture.html.twig',['form_culture'=>$form->createView()]);
    }
    #[Route('/culture/update/{id}', name: 'app_culture_update')]
    public function update(Request $request, Culture $culture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CultureType::class, $culture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Culture modifiée avec succès');
            return $this->redirectToRoute('app_culture');
        }

        return $this->render('culture/update.html.twig', [
            'form' => $form->createView(),
            'culture' => $culture
        ]);
    }

    #[Route('/culture/delete/{id}', name: 'app_culture_delete')]
    public function delete(Culture $culture, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($culture);
        $entityManager->flush();
        
        $this->addFlash('success', 'Culture supprimée avec succès');
        return $this->redirectToRoute('app_culture');
    }
    #[Route('/api/cultures/search', name: 'api_cultures_search')]
    public function searchCultures(Request $request, CultureRepository $cultureRepository): JsonResponse
    {
        $term = $request->query->get('term');
        $cultures = $cultureRepository->createQueryBuilder('c')
            ->where('c.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    
        $results = [];
        foreach ($cultures as $culture) {
            $results[] = [
                'id' => $culture->getId(),
                'text' => $culture->getNom()
            ];
        }
    
        return new JsonResponse(['results' => $results]);
    }

}
