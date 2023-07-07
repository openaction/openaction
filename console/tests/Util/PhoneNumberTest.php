<?php

namespace App\Tests\Util;

use App\Util\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function provideFormat()
    {
        yield 'null' => [null, null];
        yield 'valid' => [PhoneNumberUtil::getInstance()->parse('+33 7 57 59 46 25'), '+33 7 57 59 46 25'];
    }

    /**
     * @dataProvider provideFormat
     */
    public function testFormat(?\libphonenumber\PhoneNumber $number, ?string $expectedNumber)
    {
        $this->assertSame($expectedNumber, PhoneNumber::format($number));
    }

    public function provideFormatDatabase()
    {
        yield 'null' => [null, null];
        yield 'valid' => [PhoneNumberUtil::getInstance()->parse('+33 7 57 59 46 25'), '+33757594625'];
    }

    /**
     * @dataProvider provideFormatDatabase
     */
    public function testFormatDatabase(?\libphonenumber\PhoneNumber $number, ?string $expectedNumber)
    {
        $this->assertSame($expectedNumber, PhoneNumber::formatDatabase($number));
    }

    public function provideParse()
    {
        yield 'international-trimmed' => ['+33757594625', 'FR'];
        yield 'international-spaced' => ['+33 7 57 59 46 25', 'FR'];
        yield 'international-points' => ['+33.7.57.59.46.25', 'FR'];
        yield 'international-characters' => ['+33-7-57-59-46-25', 'FR'];
        yield 'national-trimmed' => ['0757594625', 'FR'];
        yield 'national-spaced' => ['07 57 59 46 25', 'FR'];
        yield 'national-points' => ['07.57.59.46.25', 'FR'];
        yield 'national-characters' => ['07-57-59-46-25', 'FR'];
    }

    /**
     * @dataProvider provideParse
     */
    public function testParse(string $number, string $country)
    {
        $this->assertSame(
            'Country Code: 33 National Number: 757594625',
            (string) PhoneNumber::parse($number, $country)
        );
    }
}
