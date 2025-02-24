<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
    

    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
public function update(
    Request $request,
    EntityManagerInterface $entityManager,
    CsrfTokenManagerInterface $csrfTokenManager
): Response {
    // Validate CSRF token
    $submittedToken = $request->request->get('_token');
    if (!$csrfTokenManager->isTokenValid(new CsrfToken('update_profile', $submittedToken))) {
        $this->addFlash('error', 'Invalid CSRF token.');
        return $this->redirectToRoute('app_profile');
    }

    $user = $this->getUser();

    // Handle profile picture upload
    if ($request->files->has('profile_picture')) {
        $file = $request->files->get('profile_picture');
        if ($file) {
            if ($file->isValid()) {
                // Check file size (2MB)
                $maxSize = 2 * 1024 * 1024;
                if ($file->getSize() > $maxSize) {
                    $this->addFlash('error', 'File size must be less than 2MB.');
                } else {
                    // Generate a unique filename and move the file
                    $fileName = uniqid('profile_') . '.' . $file->guessExtension();
                    $file->move(
                        $this->getParameter('upload_directory'), // Define this in services.yaml
                        $fileName
                    );
                    // Set the file path instead of base64
                    $user->setProfilePicture('/uploads/' . $fileName);
                }
            } else {
                $this->addFlash('error', 'Error uploading file: ' . $file->getErrorMessage());
            }
        }
    }

    // Update other user fields
    $user->setUsername($request->request->get('username'));
    $user->setEmail($request->request->get('email'));
    $user->setName($request->request->get('name'));
    $user->setSurname($request->request->get('surname'));
    $user->setCin($request->request->get('cin'));

    // Handle password update (add proper hashing and current password verification)
    // if ($request->request->get('new_password')) {
    //     $user->setPassword($hashedPassword);
    // }

    $entityManager->flush();

    $this->addFlash('success', 'Profile updated successfully!');
    return $this->redirectToRoute('app_profile');
}
}