<?php

namespace App\Security\Csrf;

use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/*
 * Manage and help the usage of a single global CSRF token for action links and AJAX calls.
 */
class GlobalCsrfTokenManager implements ServiceSubscriberInterface
{
    private const GLOBAL_TOKEN_INTENTION = 'console_action';

    private ContainerInterface $locator;

    // Lazy to avoid connecting to the session storage if not necessary
    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedServices(): array
    {
        return [
            CsrfTokenManagerInterface::class,
        ];
    }

    public function getToken(): CsrfToken
    {
        return $this->getCsrfTokenManager()->getToken(self::GLOBAL_TOKEN_INTENTION);
    }

    public function refreshToken(): CsrfToken
    {
        return $this->getCsrfTokenManager()->refreshToken(self::GLOBAL_TOKEN_INTENTION);
    }

    public function removeToken()
    {
        return $this->getCsrfTokenManager()->removeToken(self::GLOBAL_TOKEN_INTENTION);
    }

    public function isTokenValid(?string $token): bool
    {
        return $this->getCsrfTokenManager()->isTokenValid(new CsrfToken(self::GLOBAL_TOKEN_INTENTION, $token));
    }

    private function getCsrfTokenManager(): CsrfTokenManagerInterface
    {
        return $this->locator->get(CsrfTokenManagerInterface::class);
    }
}
