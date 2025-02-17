<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

final class ResetController extends AbstractController
{
   #[Route('/reset', name: 'app_reset', methods: ['GET'])]
   public function index(): Response
   {
       return $this->render('reset.html.twig', [
           'controller_name' => 'ResetController',
       ]);
   }

   #[Route('/reset-password', name: 'send_reset_link', methods: ['POST'])]
   public function resetPassword(
       Request $request,
       EntityManagerInterface $entityManager,
       UserPasswordHasherInterface $passwordHasher
   ): Response {
       $email = $request->request->get('email');
       $newPassword = $request->request->get('password');
       $confirmPassword = $request->request->get('confirm_password');

       if ($newPassword !== $confirmPassword) {
           $this->addFlash('error', 'Passwords do not match.');
           return $this->redirectToRoute('app_reset');
       }

       $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

       if (!$user) {
           $this->addFlash('error', 'User not found.');
           return $this->redirectToRoute('app_reset');
       }

       $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
       $user->setPassword($hashedPassword);
       $entityManager->persist($user);
       $entityManager->flush();

       $this->addFlash('success', 'Password reset successfully. Please log in.');
       return $this->redirectToRoute('app_login');
   }
}