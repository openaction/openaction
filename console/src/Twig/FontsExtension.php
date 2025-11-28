<?php

namespace App\Twig;

use App\Platform\Fonts;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FontsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_platform_fonts', [$this, 'getPlatformFonts']),
        ];
    }

    public function getPlatformFonts(): array
    {
        return Fonts::getLocalCss();
    }
}
