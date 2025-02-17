<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // Get the logged-in user
        $user = $this->getUser();

        // Example data - you would typically get this from your database
        $recentOrders = [
            ['id' => 45678, 'date' => '2024-03-15', 'total' => 89.99, 'status' => 'Delivered'],
            ['id' => 45679, 'date' => '2024-03-18', 'total' => 129.99, 'status' => 'Processing'],
        ];

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'recent_orders' => $recentOrders,
        ]);
    }
}