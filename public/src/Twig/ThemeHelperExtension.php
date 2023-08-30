<?php

namespace App\Twig;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ThemeHelperExtension extends AbstractExtension
{
    private CitipoInterface $citipo;
    private RequestStack $requestStack;

    public function __construct(CitipoInterface $citipo, RequestStack $requestStack)
    {
        $this->citipo = $citipo;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('citipo_theme_asset_url', [$this, 'getThemeAssetUrl']),
            new TwigFunction('citipo_project_asset_url', [$this, 'getProjectAssetUrl']),
            new TwigFunction('citipo_page', [$this, 'getPageContent'], ['is_safe' => ['html']]),
        ];
    }

    public function getThemeAssetUrl(string $pathname): ?string
    {
        return $this->getRequest()?->attributes->get('project')->theme_assets[$pathname] ?? null;
    }

    public function getProjectAssetUrl(string $pathname): ?string
    {
        return $this->getRequest()?->attributes->get('project')->project_assets[$pathname] ?? null;
    }

    public function getPageContent(string $id): ?string
    {
        return $this->citipo->getPage($this->getApiToken(), $id)?->content;
    }

    private function getApiToken(): ?string
    {
        return $this->getRequest()?->attributes->get('api_token');
    }

    private function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
