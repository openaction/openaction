<?php

namespace App\Tests\Stubs;

use ParagonIE\ConstantTime\Base32;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;

class TotpAuthenticatorStub implements TotpAuthenticatorInterface
{
    public function checkCode(TwoFactorInterface $user, string $code): bool
    {
        return '123456' === $code;
    }

    public function getQRContent(TwoFactorInterface $user): string
    {
        return 'secret';
    }

    public function generateSecret(): string
    {
        return Base32::encodeUpperUnpadded(random_bytes(32));
    }
}
