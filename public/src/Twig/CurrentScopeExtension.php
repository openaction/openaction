<?php

namespace App\Twig;

use App\Client\Model\ApiResource;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CurrentScopeExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_current_project', [$this, 'getCurrentProject']),
        ];
    }

    public function getCurrentProject(): ?ApiResource
    {
        return $this->requestStack->getCurrentRequest()->attributes->get('project');
    }
}
