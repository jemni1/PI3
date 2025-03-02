<?php
// src/EventSubscriber/MfaSetupSubscriber.php
namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MfaSetupSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;
    private $urlGenerator;
    private $authChecker;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->authChecker = $authChecker;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        // Skip for certain routes
        $currentRoute = $request->attributes->get('_route');
        $excludedRoutes = [
            'app_logout', 'app_login', 'app_2fa_setup', 'app_2fa_enable',
            'app_2fa_verify', 'app_2fa_check', 'app_2fa', 'app_2fa_form'
        ];
        
        if (in_array($currentRoute, $excludedRoutes)) {
            return;
        }

        // Check if user is logged in and has a TOTP secret
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return;
        }

        // If user is fully authenticated and has TOTP secret but has never completed setup
        // (you may need to add a field to track if setup is complete)
        if ($this->authChecker->isGranted('IS_AUTHENTICATED_FULLY') &&
            $user->getTotpSecret() &&
            !$user->isMfaSetupComplete()) { // Add this method to User entity
            
            // Redirect to MFA setup page
            $url = $this->urlGenerator->generate('app_2fa_setup');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}