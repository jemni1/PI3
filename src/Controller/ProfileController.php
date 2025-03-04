<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
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
    public function updateProfile(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        SluggerInterface $slugger
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cin = $request->request->get('cin');
        $username = $request->request->get('username');
        $name = $request->request->get('name');
        $surname = $request->request->get('surname');
        $email = $request->request->get('email');
        $currentPassword = $request->request->get('current_password');
        $newPassword = $request->request->get('new_password');
        $dataChanged = false;

        // Username check
        if ($username !== $user->getUsername()) {
            $existingUser = $this->entityManager
                ->getRepository('App:User')
                ->findOneBy(['username' => $username]);
                
            if ($existingUser) {
                $this->addFlash('error', 'Username already exists.');
                return $this->redirectToRoute('app_profile');
            }
            $user->setUsername($username);
            $dataChanged = true;
        }

        // Email check
        if ($email !== $user->getEmail()) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Invalid email format.');
                return $this->redirectToRoute('app_profile');
            }
            
            $existingEmail = $this->entityManager
                ->getRepository('App:User')
                ->findOneBy(['email' => $email]);
                
            if ($existingEmail) {
                $this->addFlash('error', 'Email already exists.');
                return $this->redirectToRoute('app_profile');
            }
            $user->setEmail($email);
            $dataChanged = true;
        }

        // CIN check
        if ($cin !== $user->getCin()) {
            if (!preg_match('/^\d{8}$/', $cin)) {
                $this->addFlash('error', 'CIN must be 8 digits.');
                return $this->redirectToRoute('app_profile');
            }
            
            $existingCin = $this->entityManager
                ->getRepository('App:User')
                ->findOneBy(['cin' => $cin]);
                
            if ($existingCin && $existingCin->getId() !== $user->getId()) {
                $this->addFlash('error', 'CIN already exists.');
                return $this->redirectToRoute('app_profile');
            }
            $user->setCin($cin);
            $dataChanged = true;
        }

        // Name and surname updates
        if ($name !== $user->getName()) {
            $user->setName($name);
            $dataChanged = true;
        }

        if ($surname !== $user->getSurname()) {
            $user->setSurname($surname);
            $dataChanged = true;
        }

        // Profile picture handling
        $profilePicture = $request->files->get('profile_picture');
        if ($profilePicture) {
            // ... (existing profile picture handling code remains the same)
            // Keep the existing file upload logic here
        }

        // Password update logic
        if ($currentPassword || $newPassword) {
            if (!$currentPassword || !$newPassword) {
                $this->addFlash('error', 'Both password fields are required.');
                return $this->redirectToRoute('app_profile');
            }
            
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Incorrect current password.');
                return $this->redirectToRoute('app_profile');
            }
            
            if (strlen($newPassword) < 8 || 
                !preg_match('/[A-Z]/', $newPassword) || 
                !preg_match('/[a-z]/', $newPassword) || 
                !preg_match('/[0-9]/', $newPassword)) {
                $this->addFlash('error', 'Password must be 8+ chars with uppercase, lowercase, and number.');
                return $this->redirectToRoute('app_profile');
            }
            
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $dataChanged = true;
        }

        // Validation and final save
        if (!$dataChanged) {
            $this->addFlash('info', 'No changes detected.');
            return $this->redirectToRoute('app_profile');
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('app_profile');
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'Profile updated successfully!');
        return $this->redirectToRoute('app_profile');
    }
}