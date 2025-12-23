<?php

namespace App\Listener;

use App\Client\CitipoInterface;
use App\Client\Model\ApiResource;
use App\Client\PassKey\TokenResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class CurrentProjectListener implements EventSubscriberInterface
{
    private TokenResolver $tokenResolver;
    private CitipoInterface $citipo;

    public function __construct(TokenResolver $tokenResolver, CitipoInterface $citipo)
    {
        $this->tokenResolver = $tokenResolver;
        $this->citipo = $citipo;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1024],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ('/health' === $request->getPathInfo()) {
            return;
        }

        if (!$apiToken = $this->tokenResolver->resolveProjectToken($request->getHost())) {
            throw new NotFoundHttpException('Project domain not found.');
        }

        if (!$project = $this->citipo->getProject($apiToken)) {
            throw new NotFoundHttpException('Project resource not found for domain.');
        }

        $this->checkAccessControl($project, $request, $event);

        $request->attributes->set('api_token', $apiToken);
        $request->attributes->set('project', $project);
        $request->setLocale($project->locale);
    }

    private function checkAccessControl(ApiResource $project, Request $request, RequestEvent $event)
    {
        if (!$project->access['username'] || !$project->access['password']) {
            return;
        }

        $user = $request->headers->get('php_auth_user');
        $pass = $request->headers->get('php_auth_pw');

        if ($user === $project->access['username'] && $pass === $project->access['password']) {
            return;
        }

        $response = new Response('Unauthorized', 401);
        $response->headers->set('WWW-Authenticate', 'Basic realm="'.$project->name.'"');

        $event->setResponse($response);
    }
}
