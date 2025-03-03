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
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // If already fully authenticated, redirect to home
        if ($this->getUser() && 
            (!method_exists($this->getUser(), 'isMfaEnabled') || 
             !$this->getUser()->isMfaEnabled() || 
             $request->getSession()->get('2fa_complete'))) {
            return $this->redirectToRoute('app_home');
        }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

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
        
        // If 2FA is already completed, go to home
        if ($request->getSession()->get('2fa_complete', false)) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->isMethod('POST')) {
            // Here you would validate the 2FA code
            $code = $request->request->get('code');
            $isValid2fa = $this->verify2faCode($user, $code);
            
            if ($isValid2fa) {
                $request->getSession()->set('2fa_complete', true);
                return $this->redirectToRoute('app_home');
            }
            
            // If 2FA fails, add error handling
            $this->addFlash('error', 'Invalid 2FA code');
        }

        return $this->render('2fa_login.html.twig');
    }
    
    // Example method for 2FA verification - implement your actual validation logic
    private function verify2faCode($user, $code): bool
    {
        // Replace with your actual 2FA verification
        return true; // This is just a placeholder
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}