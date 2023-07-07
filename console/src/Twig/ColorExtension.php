<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ColorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('darken', [$this, 'darken']),
            new TwigFilter('opacity', [$this, 'opacity']),
        ];
    }

    public function darken(string $hex, float $percent): string
    {
        $newHex = '';
        $percent = -$percent;

        for ($i = 0; $i < 3; ++$i) {
            $dec = hexdec(substr($hex, $i * 2, 2));
            $dec = min(max(0, $dec + $dec * $percent), 255);
            $newHex .= str_pad(dechex((int) $dec), 2, '0', STR_PAD_LEFT);
        }

        return $newHex;
    }

    public function opacity(string $hex, float $percent): string
    {
        $parts = array_map('hexdec', [$hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]]);

        return 'rgba('.implode(',', $parts).', '.$percent.')';
    }
}
