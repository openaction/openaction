<?php

namespace App\Form\User\Model;

use App\Entity\User;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TwoFactorData
{
    private TotpAuthenticatorInterface $totpAuthenticator;
    private User $user;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6, max: 6)]
    public ?string $code = null;

    public function __construct(TotpAuthenticatorInterface $totpAuthenticator, User $user)
    {
        $this->totpAuthenticator = $totpAuthenticator;
        $this->user = $user;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->totpAuthenticator->checkCode($this->user, $this->code)) {
            $context
                ->buildViolation('console.user.two_factor.invalid_code')
                ->setParameter('%code%', $this->code)
                ->atPath('code')
                ->addViolation()
            ;
        }
    }
}
