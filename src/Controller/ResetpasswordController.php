<?php

// src/Controller/ResetpasswordController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResetpasswordController extends AbstractController
{
    #[Route('/resetpassword', name: 'reset_password')]
    public function index(): Response
    {
        return $this->render('resetpassword/index.html.twig', [
            'controller_name' => 'ResetpasswordController',
        ]);
    }
}
