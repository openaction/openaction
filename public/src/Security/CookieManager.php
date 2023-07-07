<?php

namespace App\Security;

use App\Client\Model\ApiResource;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieManager
{
    public const COOKIE_NAME = 'citipo_auth_token';

    public function createAuthCookie(ApiResource $authToken): Cookie
    {
        return new Cookie(
            self::COOKIE_NAME,
            json_encode([
                'firstName' => $authToken->firstName,
                'lastName' => $authToken->lastName,
                'nonce' => $authToken->nonce,
                'encrypted' => $authToken->encrypted,
            ], JSON_THROW_ON_ERROR),
            new \DateTime('+7 days'),
            '/',
            null,
            true,
            false,
        );
    }

    public function createLogoutCookie(): Cookie
    {
        return new Cookie(self::COOKIE_NAME, '', new \DateTime('now'), '/', null, true, false);
    }

    public function readAuthToken(Request $request): ?array
    {
        if (!$cookie = $request->cookies->get(self::COOKIE_NAME)) {
            return null;
        }

        return json_decode($cookie, true, 512, JSON_THROW_ON_ERROR);
    }
}
