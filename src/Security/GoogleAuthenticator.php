<?php
namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Psr\Log\LoggerInterface;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;
    private UserRepository $userRepository;
    private LoggerInterface $logger;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        UserRepository $userRepository,
        LoggerInterface $logger
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function supports(Request $request): ?bool
    {
        $route = $request->attributes->get('_route');
        $this->logger->info('GoogleAuthenticator::supports checking route: ' . $route);
        return $route === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $this->logger->info('GoogleAuthenticator::authenticate started');
        
        try {
            /** @var GoogleClient $client */
            $client = $this->clientRegistry->getClient('google');
            $accessToken = $this->fetchAccessToken($client);
            
            $this->logger->info('Access token obtained successfully');
            
            return new SelfValidatingPassport(
                new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                    /** @var GoogleUser $googleUser */
                    $googleUser = $client->fetchUserFromToken($accessToken);
                    
                    $email = $googleUser->getEmail();
                    $this->logger->info('Google user email: ' . $email);
                    
                    // 1) Check if the user already exists
                    $existingUser = $this->userRepository->findOneBy(['email' => $email]);
                    
                    if ($existingUser) {
                        $this->logger->info('Existing user found: ' . $existingUser->getEmail());
                        // Update Google ID if not set
                        if (!$existingUser->getGoogleId()) {
                            $existingUser->setGoogleId($googleUser->getId());
                            $this->entityManager->flush();
                        }
                        return $existingUser;
                    }
                    
                    // User doesn't exist, create one
                    $this->logger->info('Creating new user from Google account');
                    $user = new User();
                    $user->setEmail($email);
                    $user->setGoogleId($googleUser->getId());
                    
                    // Set name, surname, etc.
                    $firstName = $googleUser->getFirstName() ?? '';
                    $lastName = $googleUser->getLastName() ?? '';
                    $user->setName($firstName);
                    $user->setSurname($lastName);
                    
                    // Generate username from email
                    $username = strtok($email, '@');
                    $user->setUsername($username);
                    
                    // Set a random password for Google users
                    $user->setPassword(bin2hex(random_bytes(16)));

                    // Automatically set default values for required fields if they are null
                    if (!$user->getCin()) {
                        $user->setCin('temporary-cin-' . uniqid()); // Set a temporary unique value for 'cin'
                    }

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    
                    return $user;
                })
            );
        } catch (\Exception $e) {
            $this->logger->error('Error during Google authentication: ' . $e->getMessage());
            $this->logger->error('Exception trace: ' . $e->getTraceAsString());
            throw new AuthenticationException('Google authentication failed: ' . $e->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $this->logger->info('Google authentication successful');
        
        /** @var User $user */
        $user = $token->getUser();
        
        if ($user->getIsProfileUpdated()) {
            $this->logger->info('Profile already updated, redirecting to home');
            return new RedirectResponse($this->router->generate('app_home'));
        }
        
        $this->logger->info('Profile not updated yet, redirecting to profile update page');
        return new RedirectResponse($this->router->generate('app_update_profile'));
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $this->logger->error('Authentication failure: ' . $exception->getMessage());
        
        // Redirect to the login page if authentication fails
        return new RedirectResponse($this->router->generate('app_login'));
    }
    
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $this->logger->info('GoogleAuthenticator::start called');
        
        // If authentication is needed, redirect to the login page
        return new RedirectResponse($this->router->generate('app_login'));
    }
}