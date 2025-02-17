<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        // Get the currently authenticated user
        $user = $this->getUser();

        // Render the template and pass the user data
        return $this->render('profile/index.html.twig', [
            'user' => $user, // Pass the user object to the template
        ]);
    }
}

