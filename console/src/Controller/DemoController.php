<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class DemoController extends AbstractController
{
    private UserRepository $repository;
    private TokenStorageInterface $tokenStorage;
    private RequestStack $requestStack;
    private EventDispatcherInterface $dispatcher;

    public function __construct(UserRepository $ur, TokenStorageInterface $ts, RequestStack $rs, EventDispatcherInterface $d)
    {
        $this->repository = $ur;
        $this->tokenStorage = $ts;
        $this->requestStack = $rs;
        $this->dispatcher = $d;
    }

    #[Route('/demo-login/OTg1YzI4ZTItZWYyZi')]
    public function demoLogin(Request $request)
    {
        if (!$demoUserEmail = $this->getParameter('demo_user')) {
            throw $this->createNotFoundException();
        }

        if (!$user = $this->repository->findOneByEmail($demoUserEmail)) {
            throw $this->createNotFoundException();
        }

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);

        $this->requestStack->getSession()->set('_security_main', serialize($token));
        $this->dispatcher->dispatch(new InteractiveLoginEvent($request, $token));

        return $this->redirectToRoute('homepage_redirect');
    }
}
