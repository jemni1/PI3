<?php
// src/Service/TotpAuthenticatorService.php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;

class TotpAuthenticatorService
{
    private $totpAuthenticator;
    private $entityManager;

    public function __construct(
        TotpAuthenticatorInterface $totpAuthenticator,
        EntityManagerInterface $entityManager
    ) {
        $this->totpAuthenticator = $totpAuthenticator;
        $this->entityManager = $entityManager;
    }

    public function generateSecret(User $user): string
    {
        // Generate a secret
        $secret = $this->totpAuthenticator->generateSecret();
        
        // Set it on the user
        $user->setTotpSecret($secret);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $secret;
    }

    public function getQRContent(User $user): string
    {
        return $this->totpAuthenticator->getQRContent($user);
    }
}