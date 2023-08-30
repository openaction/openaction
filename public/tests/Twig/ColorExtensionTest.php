<?php

namespace App\Tests\Twig;

use App\Twig\ColorExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ColorExtensionTest extends KernelTestCase
{
    public function provideHex2Rgba()
    {
        yield ['#000000', 1, 'rgba(0,0,0,1)'];
        yield ['000000', 0.1, 'rgba(0,0,0,0.1)'];
        yield ['#000', 1, 'rgba(0,0,0,1)'];
        yield ['000', 0.1, 'rgba(0,0,0,0.1)'];
    }

    /**
     * @dataProvider provideHex2Rgba
     */
    public function testHex2Rgba(string $hex, float $opacity, string $expected)
    {
        $this->assertSame($expected, self::getContainer()->get(ColorExtension::class)->hex2rgba($hex, $opacity));
    }
}
