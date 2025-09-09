<?php

namespace App\Twig;

use App\Entity\Project;
use App\Proxy\DomainRouter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DomainRouterExtension extends AbstractExtension
{
    private DomainRouter $router;

    public function __construct(DomainRouter $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('domain_share_url', [$this, 'generateShareUrl']),
            new TwigFunction('domain_redirect_url', [$this, 'generateRedirectUrl']),
            new TwigFunction('domain_url', [$this, 'generateUrl']),
        ];
    }

    public function generateShareUrl(Project $project, string $type, string $id, string $slug): string
    {
        return $this->router->generateShareUrl($project, $type, $id, $slug);
    }

    public function generateRedirectUrl(Project $project, string $type = 'home', ?string $ref = null): string
    {
        return $this->router->generateRedirectUrl($project, $type, $ref);
    }

    public function generateUrl(Project $project, string $endpoint): string
    {
        return $this->router->generateUrl($project, $endpoint);
    }
}
