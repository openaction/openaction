<?php

namespace App\Twig;

use Symfony\WebpackEncoreBundle\Twig\StimulusTwigExtension;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReactExtension extends AbstractExtension
{
    private StimulusTwigExtension $stimulusExtension;

    public function __construct(StimulusTwigExtension $stimulusExtension)
    {
        $this->stimulusExtension = $stimulusExtension;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('react_component', [$this, 'renderReactComponent'], ['needs_environment' => true, 'is_safe' => ['html_attr']]),
        ];
    }

    public function renderReactComponent(Environment $env, string $componentName, array $props = []): string
    {
        return $this->stimulusExtension->renderStimulusController($env, 'react', [
            'component' => $componentName,
            'props' => $props,
        ]);
    }
}
