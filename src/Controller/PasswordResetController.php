<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\{EmailType, PasswordType, SubmitType, TextType};
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\MailerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Mime\Email;

class PasswordResetController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/password-reset', name: 'app_request_password_reset')]
    public function requestPasswordReset(Request $request, MailerService $mailerService, SessionInterface $session): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send Reset Code',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                $resetCode = (string) random_int(100000, 999999);
                $user->setResetCode($resetCode);
                $user->setResetCodeExpiresAt(new \DateTime('+30 minutes'));
                $this->entityManager->flush();

                $session->set('reset_email', $email);

                // Create the Email object here
                $emailObject = (new Email())
                    ->from('monta.bellakhal10@gmail.com') // Adjust as needed
                    ->to($user->getEmail());

                // Use MailerService with HTML rendering
                $mailerService->sendEmail(
                    $emailObject,
                    'Password Reset Verification Code',
                    '', // Empty content since we're using HTML
                    true, // isHtml flag
                    $user,
                    (int) $resetCode // Cast to int for consistency with Twig template
                );

                return $this->redirectToRoute('app_verify_reset_code');
            }
            $this->addFlash('danger', 'Email not found.');
        }

        return $this->render('password_reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/password-reset/update', name: 'app_verify_reset_code')]
    public function verifyResetCode(Request $request, SessionInterface $session): Response
    {
        $email = $session->get('reset_email');
        if (!$email) {
            return $this->redirectToRoute('app_request_password_reset');
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            $session->remove('reset_email');
            return $this->redirectToRoute('app_request_password_reset');
        }

        $form = $this->createFormBuilder()
            ->add('verification_code', TextType::class, [
                'label' => 'Verification Code',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6, 'max' => 6]),
                    new Assert\Regex(['pattern' => '/^[0-9]+$/']),
                ],
            ])
            ->add('verify', SubmitType::class, [
                'label' => 'Verify Code',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $enteredCode = $form->get('verification_code')->getData();
            $storedCode = $user->getResetCode();

            if (!$storedCode) {
                $this->addFlash('danger', 'No reset code found.');
                return $this->redirectToRoute('app_request_password_reset');
            }

            if ($user->getResetCodeExpiresAt() < new \DateTime()) {
                $this->addFlash('danger', 'Reset code has expired. Please request a new one.');
                return $this->redirectToRoute('app_request_password_reset');
            }

            if ($enteredCode === $storedCode) {
                $session->set('code_verified', true);
                return $this->redirectToRoute('app_reset_password');
            } else {
                $this->addFlash('danger', 'Invalid verification code. Please try again.');
            }
        }

        return $this->render('password_reset/verify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/password-reset/reset', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function showResetForm(Request $request, SessionInterface $session): Response
    {
        if (!$session->get('code_verified')) {
            return $this->redirectToRoute('app_request_password_reset');
        }

        $email = $session->get('reset_email');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $session->clear();
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_request_password_reset');
        }

        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class, [
                'label' => 'New Password',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8]),
                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirm Password',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Reset Password',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['password'] !== $data['confirm_password']) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->render('password_reset/reset.html.twig', ['form' => $form->createView()]);
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
            $user->setResetCode(null);
            $user->setResetCodeExpiresAt(null);
            $this->entityManager->flush();

            $session->clear();
            $this->addFlash('success', 'Password reset successfully. Please log in.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('password_reset/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}