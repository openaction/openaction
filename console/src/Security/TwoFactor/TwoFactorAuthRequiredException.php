<?php

namespace App\Security\TwoFactor;

use App\Entity\Organization;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TwoFactorAuthRequiredException extends UnauthorizedHttpException
{
    private Organization $organization;

    public function __construct(Organization $organization)
    {
        parent::__construct('This organization requires Two Factor Authentication and you have not enabled it.');

        $this->organization = $organization;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }
}
