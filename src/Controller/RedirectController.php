<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

class RedirectController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function redirectAfterLogin(RequestStack $requestStack): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $session = $requestStack->getSession();
        $session->set('user_id', $user->getId());
        $roles = $user->getRoles();
        $cin = $user->getCin();
        $isProfileUpdated = $user->getIsProfileUpdated();

        // Redirect to update-profile only if:
        // 1. Profile hasn't been updated yet AND
        // 2. Either the user has ONLY ROLE_USER OR CIN is missing/invalid
        if (!$isProfileUpdated && 
            ((count($roles) === 1 && in_array('ROLE_USER', $roles)) || 
             !$cin || 
             !preg_match('/^\d{8}$/', $cin))) {
            return $this->redirectToRoute('app_update_profile');
        }

        // If we reach here, either profile is updated or no update is needed, redirect based on role
        return match (true) {
            in_array('ROLE_ADMIN', $roles) => $this->redirectToRoute('app_terrain'),
            in_array('ROLE_AGRICULTURE', $roles) => $this->redirectToRoute('app_terrain'),
            in_array('ROLE_CLIENT', $roles) => $this->redirectToRoute('listprod'),
            in_array('ROLE_WORKER', $roles) => $this->redirectToRoute('admin_recyclage_list'),
            default => $this->redirectToRoute('app_login'),
        };
    }
}