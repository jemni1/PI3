<?php
use App\Form\PasswordResetType;

class PasswordResetController extends AbstractController
{
    #[Route('/password-reset', name: 'request_password_reset')]
    public function requestPasswordReset(
        Request $request, 
        MailerService $mailerService,
        SessionInterface $session
    ): Response 
    {
        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('danger', 'No account found with this email.');
                return $this->redirectToRoute('request_password_reset');
            }

            $verificationCode = random_int(100000, 999999);
            $session->set('reset_code', $verificationCode);
            $session->set('reset_email', $email);

            try {
                $emailSent = $mailerService->sendEmail(
                    $email,
                    'Your Password Reset Code',
                    "Your verification code is: $verificationCode"
                );

                if ($emailSent) {
                    $this->addFlash('success', 'Verification code sent! Please check your email.');
                    return $this->redirectToRoute('verify_reset_code');
                }

                $this->addFlash('danger', 'Failed to send verification code. Please try again.');
                
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred while sending the verification code.');
            }
        }

        return $this->render('password_reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }
}