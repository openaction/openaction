<?php

namespace App\Twig;

use App\DataExposer\DataExposer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataExposerExtension extends AbstractExtension
{
    private DataExposer $exposer;

    public function __construct(DataExposer $exposer)
    {
        $this->exposer = $exposer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('expose', [$this, 'exposeData']),
            new TwigFunction('get_exposed_data', [$this, 'getExposedData']),
        ];
    }

    public function exposeData(string $name, $value)
    {
        $this->exposer->expose($name, $value);
    }

    public function getExposedData(): array
    {
        return $this->exposer->getExposedData();
    }
}
