<?php

// src/Controller/StatistiquesController.php
namespace App\Controller;

use App\Repository\CommandesRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/admin/statistiques', name: 'admin_statistiques')]
    public function index(CommandesRepository $commandesRepo): Response
    {
        $stats = [
            'ventesParMois' => $commandesRepo->getMonthlySales(),
            'topProduits' => $commandesRepo->getTopProducts(),
            'ventesDerniersJours' => $commandesRepo->getDailySales(),
            'statutCommandes' => $commandesRepo->getStatutStats(),
        ];

        return $this->render('page/back.html.twig', [
            'stats' => $stats
        ]);
    }

    #[Route('/api/statistiques', name: 'api_statistiques')]
    public function apiStats(CommandesRepository $commandesRepo): JsonResponse
    {
        $stats = [
            'ventesParMois' => $commandesRepo->getMonthlySales(),
            'topProduits' => $commandesRepo->getTopProducts(),
            'ventesDerniersJours' => $commandesRepo->getDailySales(),
            'statutCommandes' => $commandesRepo->getStatutStats(),
        ];

        return $this->json($stats);
    }
}