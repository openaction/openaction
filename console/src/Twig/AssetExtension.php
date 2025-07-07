<?php

namespace App\Twig;

use App\Util\Json;
use Symfony\WebpackEncoreBundle\Twig\StimulusTwigExtension;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    public function __construct(
        private readonly StimulusTwigExtension $stimulusExtension,
        private readonly string $projectDir,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('legacy_react_component', [$this, 'renderLegacyReactComponent'], ['needs_environment' => true, 'is_safe' => ['html_attr']]),
            new TwigFunction('modern_react_component', [$this, 'renderModernReactComponent'], ['needs_environment' => true, 'is_safe' => ['html_attr']]),
            new TwigFunction('get_modern_manifest', [$this, 'getModernManifest']),
        ];
    }

    public function renderLegacyReactComponent(Environment $env, string $componentName, array $props = []): string
    {
        return $this->stimulusExtension->renderStimulusController($env, 'legacy-react', [
            'component' => $componentName,
            'props' => $props,
        ]);
    }

    public function renderModernReactComponent(Environment $env, string $componentName, array $props = []): string
    {
        return $this->stimulusExtension->renderStimulusController($env, 'modern-react', [
            'component' => $componentName,
            'props' => $props,
        ]);
    }

    public function getModernManifest(): array
    {
        return Json::decode(file_get_contents($this->projectDir.'/public/build-modern/.vite/manifest.json'));
    }
}
