<?php

namespace App\Controller\Membership;

use App\Client\CitipoInterface;
use App\Client\Model\ApiResource;
use App\Controller\AbstractController;
use App\Security\CookieManager;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractMembershipController extends AbstractController
{
    protected function authorize(Request $request): ?ApiResource
    {
        if (!$authToken = $this->getAuthToken($request)) {
            return null;
        }

        if (!$contact = $this->container->get(CitipoInterface::class)->authorize($this->getApiToken(), $authToken)) {
            return null;
        }

        return $contact;
    }

    protected function getAuthToken(Request $request): ?array
    {
        return $this->container->get(CookieManager::class)->readAuthToken($request);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            CitipoInterface::class,
            CookieManager::class,
        ]);
    }
}
