<?php

namespace App\Tests\Util;

use App\Util\Uid;
use PHPUnit\Framework\TestCase;

class UidTest extends TestCase
{
    public function testFromToUid()
    {
        $uuid = Uid::random();
        $this->assertSame($uuid->toRfc4122(), Uid::fromBase62(Uid::toBase62($uuid))->toRfc4122());
    }

    public function provideUuids()
    {
        yield ['6XwNYIVTWDY6oWyA29jtnZ', Uid::fixed('a')];
        yield ['4EwRbfDrTc35kcHacXOVrs', Uid::fixed('b')];
        yield ['9sSohE1xy6H5mGU7hKihZ', Uid::fixed('c')];
    }

    /**
     * @dataProvider provideUuids
     */
    public function testDecode($encoded, $expected)
    {
        $this->assertSame((string) $expected, Uid::fromBase62($encoded)->toRfc4122());
    }

    public function testDecodeInvalid()
    {
        $this->assertNull(Uid::fromBase62('7rnedzqzqk0hv5ktdm3a1m'));
    }
}
