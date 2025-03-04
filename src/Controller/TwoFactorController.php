<?php
namespace App\Controller;

use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class TwoFactorController extends AbstractController
{
    private const MAX_ATTEMPTS = 3;
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    #[Route('/2fa/verify', name: '2fa_verification')]
    public function display2faForm(Request $request): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();

        // Redirect fully authenticated users to home
        if ($session->get('2fa_complete', false)) {
            return $this->redirectToRoute('app_home');
        }

        // Check if user is authenticated and MFA is pending
        if (!$user instanceof UserInterface || !$session->get('mfa_pending') || !$user->isMfaEnabled()) {
            $this->addFlash('error', 'User not authenticated or MFA not enabled');
            return $this->redirectToRoute('app_login');
        }

        $otp = random_int(100000, 999999);
        $hashedOtp = password_hash((string)$otp, PASSWORD_BCRYPT);

        $session->set('2fa', [
            'code' => $hashedOtp,
            'attempts' => 0,
            'user_id' => $user->getId(),
            'created_at' => time(),
        ]);

        $this->sendOtpEmail($user, $otp);
        $this->addFlash('info', 'OTP sent to ' . $user->getEmail());

        // Retrieve reCAPTCHA site key from environment
        $recaptchaSiteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';

        $response = $this->render('two_factor_controller_php/index.html.twig', [
            'recaptchaSiteKey' => $recaptchaSiteKey
        ]);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        return $response;
    }

    #[Route('/2fa/resend', name: '2fa_resend')]
    public function resendOtp(Request $request): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();

        // Redirect fully authenticated users to home
        if ($session->get('2fa_complete', false)) {
            return $this->redirectToRoute('app_home');
        }

        // Check if user is authenticated and MFA is pending
        if (!$user instanceof UserInterface || !$session->get('mfa_pending') || !$user->isMfaEnabled()) {
            $this->addFlash('error', 'User not authenticated or MFA not enabled');
            return $this->redirectToRoute('app_login');
        }

        $otp = random_int(100000, 999999);
        $hashedOtp = password_hash((string)$otp, PASSWORD_BCRYPT);

        $session->set('2fa', [
            'code' => $hashedOtp,
            'attempts' => 0,
            'user_id' => $user->getId(),
            'created_at' => time(),
        ]);

        $this->sendOtpEmail($user, $otp);
        $this->addFlash('success', 'A new verification code has been sent to your email.');

        $response = $this->redirectToRoute('2fa_verification');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        return $response;
    }

    #[Route('/2fa/check', name: '2fa_check', methods: ['POST'])]
    public function verify2fa(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $session = $request->getSession();
        $user = $this->getUser();
        $otpData = $session->get('2fa', []);

        if (!$user || !$this->isValidOtpSession($otpData, $user) || !$session->get('mfa_pending')) {
            $session->remove('2fa');
            $session->remove('mfa_pending');
            return $this->redirectToRoute('app_login');
        }

        // Verify OTP
        $userOtp = $request->request->get('_auth_code', '');
        if ($this->isValidOtp($userOtp, $otpData)) {
            // Mark 2FA as complete
            $session->set('2fa_complete', true);
            $session->remove('mfa_pending');
            $session->remove('2fa');
            
            // Authenticate user with full privileges
            $this->authenticateUser($user, $tokenStorage);
            
            return $this->redirectToRoute('app_home');
        }

        return $this->handleFailedAttempt($session, $otpData);
    }

    private function isValidOtpSession(array $otpData, UserInterface $user): bool
    {
        if (!isset($otpData['created_at']) || (time() - $otpData['created_at']) > 600) {
            return false;
        }
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
        $tokenStorage->getToken()->setAuthenticated(true);
    }

    private function handleFailedAttempt($session, array $otpData): Response
    {
        $otpData['attempts']++;
        $session->set('2fa', $otpData);

        if ($otpData['attempts'] >= self::MAX_ATTEMPTS) {
            $session->remove('2fa');
            $session->remove('mfa_pending');
            $this->addFlash('error', 'Too many failed attempts. Please login again.');
            return $this->redirectToRoute('app_login');
        }

        $remainingAttempts = self::MAX_ATTEMPTS - $otpData['attempts'];
        $this->addFlash('error', sprintf('Invalid code. %d attempts remaining.', $remainingAttempts));
        return $this->redirectToRoute('2fa_verification');
    }

    private function sendOtpEmail(UserInterface $user, int $otp): void
    {
        $email = new Email();
        $email->to($user->getEmail());
        $email->from('no-reply@yourdomain.com'); // Replace with your domain

        $subject = 'Your Verification Code';
        
        // Using the MailerService with your verification_code.html.twig template
        $this->mailerService->sendEmail(
            $email,
            $subject,
            "Your verification code is: $otp",
            true, // isHtml
            $user,
            $otp  // resetCode - using OTP as the verification code
        );
    }
}