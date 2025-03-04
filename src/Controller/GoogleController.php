<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry, LoggerInterface $logger): Response
    {
        $logger->info('Starting Google OAuth flow');
        
        try {
            return $clientRegistry
                ->getClient('google')
                ->redirect(['profile', 'email']);
        } catch (\Exception $e) {
            $logger->error('Error starting Google OAuth: ' . $e->getMessage());
            return new RedirectResponse($this->generateUrl('app_login'));
        }
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(LoggerInterface $logger): Response
    {
        $logger->info('GoogleController::connectCheckAction reached - this should be handled by the authenticator');
        return new Response('This should never be called! The authenticator should handle this route.', 404);
    }
}