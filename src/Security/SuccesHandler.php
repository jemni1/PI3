<?php
// SuccesHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class SuccesHandler implements AuthenticationSuccessHandlerInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();
        
        // Check if MFA is enabled for this user
        if (method_exists($user, 'isMfaEnabled') && $user->isMfaEnabled()) {
            // Set MFA pending flag - will be checked by TwoFactorController
            $request->getSession()->set('mfa_pending', true);
            
            // Redirect to 2FA verification
            return new RedirectResponse($this->urlGenerator->generate('2fa_verification'));
        }
        
        // No MFA required, redirect to homepage
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}