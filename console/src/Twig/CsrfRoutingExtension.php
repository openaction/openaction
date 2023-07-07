<?php

namespace App\Twig;

use App\Security\Csrf\GlobalCsrfTokenManager;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Twig\Extension\AbstractExtension;
use Twig\Node\Node;
use Twig\TwigFunction;

/*
 * Generates URLs with a CSRF token passed as a query parameter.
 */
class CsrfRoutingExtension extends AbstractExtension
{
    private RoutingExtension $rootExtension;
    private GlobalCsrfTokenManager $csrfTokenManager;

    public function __construct(RoutingExtension $rootExtension, GlobalCsrfTokenManager $csrfTokenManager)
    {
        $this->rootExtension = $rootExtension;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_global_csrf_token', [$this, 'getToken']),
            new TwigFunction('csrf_url', [$this, 'getCsrfUrl'], ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]),
            new TwigFunction('csrf_path', [$this, 'getCsrfPath'], ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]),
        ];
    }

    public function getToken(): string
    {
        return $this->csrfTokenManager->getToken()->getValue();
    }

    public function getCsrfPath(string $name, array $parameters = [], bool $relative = false): string
    {
        $parameters['_token'] = $this->csrfTokenManager->getToken()->getValue();

        return $this->rootExtension->getPath($name, $parameters, $relative);
    }

    public function getCsrfUrl(string $name, array $parameters = [], bool $schemeRelative = false): string
    {
        $parameters['_token'] = $this->csrfTokenManager->getToken()->getValue();

        return $this->rootExtension->getUrl($name, $parameters, $schemeRelative);
    }

    public function isUrlGenerationSafe(Node $argsNode): array
    {
        return $this->rootExtension->isUrlGenerationSafe($argsNode);
    }
}
