<?php

namespace App\Tests\Platform;

use App\Platform\PrintFiles;
use App\Platform\Products;
use App\Tests\UnitTestCase;

class PrintFilesTest extends UnitTestCase
{
    public function provideConvertPixelsToMm()
    {
        yield [Products::PRINT_CAMPAIGN_FLYER, 1862, 1576];
        yield [Products::PRINT_CAMPAIGN_FLYER, 2592, 2195];
        yield [Products::PRINT_CAMPAIGN_FLYER, 2598, 2200];
    }

    /**
     * @dataProvider provideConvertPixelsToMm
     */
    public function testConvertPixelsToMm(string $product, int $pixels, int $expected)
    {
        $this->assertSame($expected, PrintFiles::convertPixelsToMm($product, $pixels));
    }
}
