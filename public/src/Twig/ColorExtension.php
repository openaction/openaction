<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ColorExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hex2rgba', [$this, 'hex2rgba']),
        ];
    }

    public function hex2rgba(string $color, ?float $opacity = null): ?string
    {
        $color = trim($color, '#');

        if (!($size = strlen($color))) {
            return null;
        }

        if (6 === $size) {
            $hex = [$color[0].$color[1], $color[2].$color[3], $color[4].$color[5]];
        } elseif (3 === $size) {
            $hex = [$color[0].$color[0], $color[1].$color[1], $color[2].$color[2]];
        } else {
            return null;
        }

        $rgb = array_map('hexdec', $hex);

        if (null === $opacity) {
            return 'rgb('.implode(',', $rgb).')';
        }

        if (abs($opacity) > 1) {
            $opacity = 1.0;
        }

        return 'rgba('.implode(',', $rgb).','.$opacity.')';
    }
}
