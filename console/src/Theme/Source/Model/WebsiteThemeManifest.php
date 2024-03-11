<?php

namespace App\Theme\Source\Model;

class WebsiteThemeManifest
{
    private array $name;
    private array $description;
    private array $defaultColors;
    private array $defaultFonts;
    private string $thumbnail;
    private array $templates;
    private array $assets;
    private ?int $postsPerPage;
    private ?int $eventsPerPage;

    public function __construct(array $manifest)
    {
        $this->name = $manifest['name'] ?? [];
        $this->description = $manifest['description'] ?? [];
        $this->defaultColors = $manifest['defaultColors'] ?? [];
        $this->defaultFonts = $manifest['defaultFonts'] ?? [];
        $this->thumbnail = $manifest['thumbnail'] ?? '';
        $this->templates = $manifest['templates'] ?? [];
        $this->assets = $manifest['assets'] ?? [];
        $this->postsPerPage = $manifest['postsPerPage'] ?? null;
        $this->eventsPerPage = $manifest['eventsPerPage'] ?? null;
    }

    public function getName(): array
    {
        return $this->name;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function getDefaultColors(): array
    {
        return $this->defaultColors;
    }

    public function getDefaultFonts(): array
    {
        return $this->defaultFonts;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function getPostsPerPage(): ?int
    {
        return $this->postsPerPage;
    }

    public function getEventsPerPage(): ?int
    {
        return $this->eventsPerPage;
    }
}
