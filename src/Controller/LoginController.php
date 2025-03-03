<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = $this->getUser();

        // Handle successful authentication
        if ($user) {
            // Check MFA status
            if ($user->isMfaEnabled()) {
                // If MFA is enabled but not completed, redirect to 2FA page
                if (!$request->getSession()->get('2fa_complete', false)) {
                    return $this->redirectToRoute('2fa_login');
                }
                // Reset the 2fa_complete flag after successful login
                $request->getSession()->set('2fa_complete', false);
            }
            // If no MFA or MFA completed, go to home page
            return $this->redirectToRoute('app_home');
        }

        // If not authenticated, show login form
        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/2fa', name: '2fa_login', methods: ['GET', 'POST'])]
    public function twoFactorLogin(Request $request): Response
    {
        $user = $this->getUser();
        
        // Ensure user is authenticated but needs 2FA
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        if (!$user->isMfaEnabled()) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->isMethod('POST')) {
            // Here you would validate the 2FA code
            // This is a placeholder - implement your actual 2FA verification logic
            $isValid2fa = true; // Replace with actual verification
            
            if ($isValid2fa) {
                $request->getSession()->set('2fa_complete', true);
                return $this->redirectToRoute('app_home');
            }
            
            // If 2FA fails, you might want to add error handling
            $this->addFlash('error', 'Invalid 2FA code');
        }

        return $this->render('2fa_login.html.twig');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}