<?php

namespace App\Controller;

use App\Client\Model\ApiResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends BaseController
{
    public function getProject(): ?ApiResource
    {
        return $this->container->get('request_stack')->getCurrentRequest()->attributes->get('project');
    }

    public function getApiToken(): string
    {
        return $this->container->get('request_stack')->getCurrentRequest()->attributes->get('api_token');
    }

    protected function denyUnlessToolEnabled(string $tool)
    {
        if (!in_array($tool, $this->getProject()->tools, true)) {
            throw $this->createNotFoundException();
        }
    }

    protected function httpCache(int $ttl, Response $response): Response
    {
        $response->setCache([
            'public' => true,
            'max_age' => $ttl,
            's_maxage' => $ttl,
        ]);

        return $response;
    }
}
