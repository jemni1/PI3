<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class TwoFactorController extends AbstractController
{
    private const MAX_ATTEMPTS = 3;

    #[Route('/2fa', name: '2fa_login')]
    public function display2faForm(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface || !$user->isMfaEnabled()) {
            $this->addFlash('error', 'User not authenticated or MFA not enabled');
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();
        $otp = random_int(100000, 999999);
        $hashedOtp = password_hash((string)$otp, PASSWORD_BCRYPT);

        $session->set('2fa', [
            'code' => $hashedOtp,
            'attempts' => 0,
            'user_id' => $user->getId()
        ]);

        $this->addFlash('info', 'Attempting to send OTP email to ' . $user->getEmail());
        $this->sendOtpEmail($user, $otp, $mailer);

        return $this->render('two_factor_controller_php/index.html.twig');
    }

    #[Route('/2fa/resend', name: '2fa_resend')]
    public function resendOtp(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface || !$user->isMfaEnabled()) {
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();
        $otp = random_int(100000, 999999);
        $hashedOtp = password_hash((string)$otp, PASSWORD_BCRYPT);

        $session->set('2fa', [
            'code' => $hashedOtp,
            'attempts' => 0,
            'user_id' => $user->getId()
        ]);

        $this->sendOtpEmail($user, $otp, $mailer);
        $this->addFlash('success', 'A new verification code has been sent to your email.');
        return $this->redirectToRoute('2fa_login');
    }

    #[Route('/verify-2fa', name: '2fa_check', methods: ['POST'])]
    public function verify2fa(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $session = $request->getSession();
        $user = $this->getUser();
        $otpData = $session->get('2fa', []);

        if (!$user || !$this->isValidOtpSession($otpData, $user)) {
            $session->remove('2fa');
            return $this->redirectToRoute('app_login');
        }

        $userOtp = $request->request->get('_auth_code', '');
        if ($this->isValidOtp($userOtp, $otpData)) {
            $this->authenticateUser($user, $tokenStorage);
            $session->remove('2fa');
            return $this->redirectToRoute('app_home');
        }

        return $this->handleFailedAttempt($session, $otpData);
    }

    private function isValidOtpSession(array $otpData, UserInterface $user): bool
    {
        return isset($otpData['code'], $otpData['user_id']) 
            && $otpData['user_id'] === $user->getId();
    }

    private function isValidOtp(string $userOtp, array $otpData): bool
    {
        return is_numeric($userOtp) 
            && password_verify($userOtp, $otpData['code']);
    }

    private function authenticateUser(UserInterface $user, TokenStorageInterface $tokenStorage): void
    {
        $token = new UsernamePasswordToken(
            $user,
            'main',
            $user->getRoles()
        );
        $tokenStorage->setToken($token);
    }

    private function handleFailedAttempt($session, array $otpData): Response
    {
        $otpData['attempts']++;
        $session->set('2fa', $otpData);

        if ($otpData['attempts'] >= self::MAX_ATTEMPTS) {
            $session->remove('2fa');
            $this->addFlash('error', 'Too many failed attempts. Please login again.');
            return $this->redirectToRoute('app_login');
        }

        $remainingAttempts = self::MAX_ATTEMPTS - $otpData['attempts'];
        $this->addFlash('error', sprintf('Invalid code. %d attempts remaining.', $remainingAttempts));
        return $this->redirectToRoute('2fa_login');
    }

    private function sendOtpEmail(UserInterface $user, int $otp, MailerInterface $mailer): void
    {
        try {
            // Create inline HTML email template instead of using the missing Twig template
            $htmlContent = "
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #4a7aff; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
                        .content { padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
                        .code { font-size: 32px; font-weight: bold; color: #4a7aff; text-align: center; 
                                letter-spacing: 5px; margin: 20px 0; padding: 10px; background-color: #f5f5f5; }
                        .footer { margin-top: 20px; font-size: 12px; color: #999; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Your Secure Login Code</h2>
                        </div>
                        <div class='content'>
                            <p>Hello,</p>
                            <p>You recently requested to log in to your account. Please use the following verification code:</p>
                            <div class='code'>{$otp}</div>
                            <p>This code will expire in 10 minutes. Do not share this code with anyone.</p>
                            <p>If you did not request this code, please ignore this email or contact support if you have concerns.</p>
                        </div>
                        <div class='footer'>
                            <p>This is an automated message, please do not reply to this email.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $email = (new Email())
                ->from("monta.bellakhal10@gmail.com")
                ->to($user->getEmail())
                ->subject('Your Secure Login Code')
                ->html($htmlContent);

            $mailer->send($email);
            $this->addFlash('success', 'Verification code sent successfully.');
        } catch (\Exception $e) {
            // Display the actual error message for debugging
            $this->addFlash('error', 'Failed to send OTP: ' . $e->getMessage());
        }
    }
}