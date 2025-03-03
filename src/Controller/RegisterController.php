<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Retrieve reCAPTCHA site key from environment
        $recaptchaSiteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';

        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user, [
            'validation_groups' => ['registration']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // CIN validation
            $cin = $user->getCin();
            if (!preg_match('/^[0-9]{8}$/', $cin)) {
                $form->get('cin')->addError(new FormError('CIN must be exactly 8 digits.'));
            }

            // Check for existing username
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
            if ($existingUser) {
                $form->get('username')->addError(new FormError('This username is already taken.'));
            }

            // Check for existing email
            $existingEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingEmail) {
                $form->get('email')->addError(new FormError('This email is already registered.'));
            }

            // Check for existing CIN
            $existingCin = $entityManager->getRepository(User::class)->findOneBy(['cin' => $user->getCin()]);
            if ($existingCin) {
                $form->get('cin')->addError(new FormError('This CIN is already registered.'));
            }

            // Stop registration if errors exist
            if (!$form->isValid()) {
                return $this->render('register.html.twig', [
                    'form' => $form->createView(),
                    'recaptchaSiteKey' => $recaptchaSiteKey
                ]);
            }

            // Hash the password
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('password')->getData())
            );

            try {
                // Save user in database
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Registration successful! Please log in.');
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning', 'One or more fields are already in use. Please check and try again.');
            }
        }

        // Handle validation errors and display flash messages
        foreach ($form->getErrors(true) as $error) {
            if ($error->getMessage() === 'This email is already used') {
                $this->addFlash('warning', 'This email is already used.');
            }
            if ($error->getMessage() === 'This username is already taken') {
                $this->addFlash('warning', 'This username is already taken.');
            }
            if ($error->getMessage() === 'This CIN is already used') {
                $this->addFlash('warning', 'This CIN is already used.');
            }
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
            'recaptchaSiteKey' => $recaptchaSiteKey
        ]);
    }
}
