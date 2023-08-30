<?php

namespace App\Security\Registration;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ManualAuthenticator
{
    private TokenStorageInterface $tokenStorage;
    private RequestStack $requestStack;

    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function authenticate(User $user)
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $this->tokenStorage->setToken($token);
        $this->requestStack->getSession()->set('_security_main', serialize($token));
    }
}
