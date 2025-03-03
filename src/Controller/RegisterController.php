<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Retrieve the recaptcha site key from environment
        $recaptchaSiteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';

        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user, [
            'validation_groups' => ['registration']
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Hash the password
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $user->getPassword())
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Registration successful! Please log in.');
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                // Handle duplicate entry error (e.g., username already exists)
                $form->get('username')->addError(
                    new \Symfony\Component\Form\FormError('This username is already in use.')
                );
            } catch (\Exception $e) {
                // Handle other unexpected errors
                $this->addFlash('error', 'An error occurred during registration. Please try again.');
            }
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
            'recaptchaSiteKey' => $recaptchaSiteKey
        ]);
    }
}