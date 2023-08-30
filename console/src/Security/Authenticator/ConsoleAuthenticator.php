<?php

namespace App\Security\Authenticator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ConsoleAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router, CsrfTokenManagerInterface $ctm)
    {
        $this->entityManager = $em;
        $this->urlGenerator = $router;
        $this->csrfTokenManager = $ctm;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('security_login');
    }

    public function authenticate(Request $request): Passport
    {
        $payload = $request->request;
        $request->getSession()->set(Security::LAST_USERNAME, $payload->get('email'));

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $payload->get('_csrf_token')))) {
            throw new CustomUserMessageAuthenticationException('login.invalid_csrf');
        }

        if (!$user = $this->entityManager->getRepository(User::class)->findOneByEmail($payload->get('email'))) {
            throw new CustomUserMessageAuthenticationException('login.invalid_credentials');
        }

        return new Passport(
            new UserBadge($user->getEmail(), static fn () => $user),
            new PasswordCredentials($payload->get('password')),
            [new RememberMeBadge()]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('homepage_redirect'));
    }
}
