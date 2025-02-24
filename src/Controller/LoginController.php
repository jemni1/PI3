<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;

class LoginController extends AbstractController  
{
    private $authenticationUtils;
    private $security;

    public function __construct(AuthenticationUtils $authenticationUtils, Security $security)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->security = $security;
    }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]  
    public function index(): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        // Check if the user is logged in as an admin, agriculture, client, or worker and redirect accordingly
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_terrain'); // redirect to Terrain for admin
        }

        if ($this->security->isGranted('ROLE_AGRICULTURE')) {
            return $this->redirectToRoute('app_terrain'); // redirect to Terrain for agriculture role
        }

        if ($this->security->isGranted('ROLE_CLIENT')) {
            return $this->redirectToRoute('listprod'); // redirect to listprod for client role
        }

        if ($this->security->isGranted('ROLE_WORKER')) {
            return $this->redirectToRoute('admin_recyclage_list'); // redirect to admin_recyclage_list for worker role
        }

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
