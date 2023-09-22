<?php

namespace App\Security\Authenticator;

use App\Repository\Integration\TelegramAppAuthorizationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class IntegrationsApiAuthenticator extends AbstractAuthenticator
{
    private TelegramAppAuthorizationRepository $telegramRepository;

    public function __construct(TelegramAppAuthorizationRepository $telegramRepository)
    {
        $this->telegramRepository = $telegramRepository;
    }

    public function supports(Request $request): bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        if (!$auth = $request->headers->get('Authorization')) {
            throw new TokenNotFoundException();
        }

        if (!preg_match('/Bearer\s(.+)/', $auth, $matches)) {
            throw new TokenNotFoundException();
        }

        if (!$authorization = $this->telegramRepository->findOneBy(['apiToken' => trim($matches[1])])) {
            throw new BadCredentialsException();
        }

        if (!$authorization->getMember()) {
            throw new BadCredentialsException();
        }

        return new SelfValidatingPassport(new UserBadge($authorization->getApiToken(), static fn () => $authorization));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse(['error' => 'Invalid API token.'], 401);
    }
}
