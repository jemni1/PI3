<?php

namespace App\Security;

use OTPHP\TOTP;

class TotpAuthenticator implements TotpAuthenticatorInterface
{
    public function generateSecret(): string
    {
        return TOTP::generate()->getSecret();
    }

    public function getQRCode(string $secret, string $email): string
    {
        $totp = TOTP::createFromSecret($secret);
        $totp->setLabel($email);
        return $totp->getQrCodeUri();
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return TOTP::createFromSecret($secret)->verify($code);
    }
}