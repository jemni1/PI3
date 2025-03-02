<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserUpdateController extends AbstractController
{
    private $entityManager;
    private $tokenStorage;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/update-profile', name: 'app_update_profile')]
    public function update(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validate business rules
            $cin = $user->getCin();
            $isCinValid = preg_match('/^\d{8}$/', $cin);
            
            $userRoles = $user->getRoles();
            $allowedRoles = ['ROLE_AGRICULTURE', 'ROLE_WORKER', 'ROLE_CLIENT'];
            $isRoleValid = count(array_intersect($userRoles, $allowedRoles)) > 0;

            if (!$isCinValid || !$isRoleValid) {
                // Handle validation errors
                if (!$isCinValid) $this->addFlash('error', 'Invalid CIN (8 digits required)');
                if (!$isRoleValid) $this->addFlash('error', 'Select a valid role');
                return $this->render('user/index.html.twig', ['form' => $form->createView()]);
            }

            try {
                // Mark profile as updated
                $user->setIsProfileUpdated(true);

                // Save changes
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Refresh authentication token
                $token = new UsernamePasswordToken(
                    $user,
                    'main', // Must match your firewall name
                    $user->getRoles()
                );
                $this->tokenStorage->setToken($token);

                $this->addFlash('success', 'Profile updated successfully!');
                return $this->redirectToRoute('app_home');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Update failed: ' . $e->getMessage());
            }
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}