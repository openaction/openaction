<?php

namespace App\Twig;

use App\Client\CitipoInterface;
use App\Client\Model\ApiResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
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
            new TwigFunction('citipo_page_data', [$this, 'getPageData'], ['is_safe' => ['html']]),
            new TwigFunction('citipo_trombinoscope_data', [$this, 'getTrombinoscopeData'], ['is_safe' => ['html']]),
            new TwigFunction('citipo_dump', [$this, 'dump'], ['is_safe' => ['html']]),
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

    public function getPageData(string $id): ?array
    {
        return $this->citipo->getPage($this->getApiToken(), $id)?->toArray();
    }

    public function getPageContent(string $id): ?string
    {
        $page = $this->getPageData($id);

        return $page ? $page['content'] : null;
    }

    public function getTrombinoscopeData(): array
    {
        $data = [];

        /** @var ApiResource $person */
        foreach ($this->citipo->getTrombinoscope($this->getApiToken()) as $person) {
            $data[] = $person->toArray();
        }

        return $data;
    }

    public function dump(mixed $data): string
    {
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();
        $output = '';

        $dumper->dump(
            $cloner->cloneVar($data),
            function (string $line, int $depth) use (&$output): void {
                if ($depth >= 0) {
                    $output .= $line."\n";
                }
            }
        );

        return $output;
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
