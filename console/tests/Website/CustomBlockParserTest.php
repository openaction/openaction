<?php

namespace App\Tests\Website;

use App\Tests\KernelTestCase;
use App\Website\CustomBlockParser;

class CustomBlockParserTest extends KernelTestCase
{
    public function testNormalizeCustomBlocksIn(): void
    {
        self::bootKernel();

        $blockParser = static::getContainer()->get(CustomBlockParser::class);

        $this->assertStringEqualsFile(
            __DIR__.'/../Fixtures/content/cutom-block-parser-expected.html',
            $blockParser->normalizeCustomBlocksIn(file_get_contents(__DIR__.'/../Fixtures/content/cutom-block-parser-input.html')),
        );
    }
}
