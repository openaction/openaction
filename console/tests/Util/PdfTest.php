<?php

namespace App\Tests\Util;

use App\Util\Pdf;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase
{
    public function provideOpen()
    {
        yield 'campaign_door' => [__DIR__.'/../Fixtures/printing/campaign_door.pdf', 2, 66, 216];
        yield 'official_ballot' => [__DIR__.'/../Fixtures/printing/official_ballot.pdf', 1, 153, 110];
        yield 'official_banner' => [__DIR__.'/../Fixtures/printing/official_banner.pdf', 1, 426, 303];
        yield 'official_pledge' => [__DIR__.'/../Fixtures/printing/official_pledge.pdf', 4, 213, 300];
        yield 'official_poster' => [__DIR__.'/../Fixtures/printing/official_poster.pdf', 1, 600, 847];
    }

    /**
     * @dataProvider provideOpen
     */
    public function testOpen(string $pathname, int $expectedPages, int $expectedWidth, int $expectedHeight)
    {
        $pdf = Pdf::open($pathname);
        $this->assertSame($expectedPages, $pdf->getPages());
        $this->assertSame($expectedWidth, $pdf->getWidth());
        $this->assertSame($expectedHeight, $pdf->getHeight());
    }
}
