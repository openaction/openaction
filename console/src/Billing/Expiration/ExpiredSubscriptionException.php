<?php

namespace App\Billing\Expiration;

use App\Entity\Organization;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExpiredSubscriptionException extends NotFoundHttpException
{
    private Organization $organization;

    public function __construct(Organization $organization)
    {
        parent::__construct('The subscription for this organization has expired.');

        $this->organization = $organization;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }
}
