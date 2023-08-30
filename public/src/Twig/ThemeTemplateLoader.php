<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class ThemeTemplateLoader implements LoaderInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function exists(string $name): bool
    {
        return str_starts_with($name, '@theme') && null !== $this->getProjectThemeFile($name);
    }

    public function getSourceContext(string $name): Source
    {
        return new Source($this->getProjectThemeFile($name), $name, $name);
    }

    public function getCacheKey(string $name): string
    {
        return md5($name.$this->getProjectThemeFile($name));
    }

    public function isFresh(string $name, int $time): bool
    {
        return false;
    }

    private function getProjectThemeFile(string $namespacedName): ?string
    {
        if ($request = $this->requestStack->getCurrentRequest()) {
            $project = $request->attributes->get('project');

            if ($project) {
                return $project->theme[substr($namespacedName, 7)];
            }
        }

        return null;
    }
}
