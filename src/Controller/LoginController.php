<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function index(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Check if user is already authenticated
        $user = $this->getUser();
        
        // If POST and no error, authentication was successful
        if ($request->isMethod('POST') && !$error && $user) {
            // Check if MFA is enabled
            if ($user->isMfaEnabled()) {
                return $this->redirectToRoute('2fa_login');
            }
            
            // If no MFA, redirect to home
            return $this->redirectToRoute('app_home');
        }
        
        // If GET and already authenticated
        if ($request->isMethod('GET') && $user) {
            // If MFA is enabled but not completed
            if ($user->isMfaEnabled() && !$request->getSession()->get('2fa_complete', false)) {
                return $this->redirectToRoute('2fa_login');
            }
            
            // Otherwise go to home
            return $this->redirectToRoute('app_home');
        }

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}