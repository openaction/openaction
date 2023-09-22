<?php

namespace App\Security\Authenticator;

use App\Repository\ProjectRepository;
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

class ProjectsApiAuthenticator extends AbstractAuthenticator
{
    private ProjectRepository $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
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

        if (!$project = $this->repository->findOneBy(['apiToken' => trim($matches[1])])) {
            throw new BadCredentialsException();
        }

        return new SelfValidatingPassport(new UserBadge($project->getApiToken(), static fn () => $project));
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
