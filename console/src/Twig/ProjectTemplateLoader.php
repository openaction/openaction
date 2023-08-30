<?php

namespace App\Twig;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class ProjectTemplateLoader implements LoaderInterface
{
    private ProjectRepository $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function exists(string $name): bool
    {
        return str_starts_with($name, '@project') && null !== $this->getProjectThemeFile($name);
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
        if (count($parts = explode('/', $namespacedName)) < 3) {
            return null;
        }

        /** @var Project $project */
        if (!$project = $this->repository->find((int) $parts[1])) {
            return null;
        }

        return match ($parts[2]) {
            'emailing_legalities.html.twig' => $project->getEmailingLegalities().'',
            default => null,
        };
    }
}
