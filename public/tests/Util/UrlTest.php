<?php

namespace App\Tests\Util;

use App\Util\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function provideAddQueryParameter()
    {
        yield 'simple' => [
            'url' => 'https://google.com',
            'expected' => 'https://google.com/?answerId=1',
        ];

        yield 'trailing-slash' => [
            'url' => 'https://google.com/',
            'expected' => 'https://google.com/?answerId=1',
        ];

        yield 'path' => [
            'url' => 'https://google.com/path',
            'expected' => 'https://google.com/path?answerId=1',
        ];

        yield 'queryParam' => [
            'url' => 'https://google.com/path?a=b',
            'expected' => 'https://google.com/path?a=b&answerId=1',
        ];

        yield 'existingQueryParam' => [
            'url' => 'https://google.com/path?answerId=2&a=b',
            'expected' => 'https://google.com/path?answerId=1&a=b',
        ];

        yield 'no-host' => [
            'url' => '',
            'expected' => '?answerId=1',
        ];

        yield 'no-host-path' => [
            'url' => '/path',
            'expected' => '/path?answerId=1',
        ];

        yield 'no-host-queryParam' => [
            'url' => '/path?a=b',
            'expected' => '/path?a=b&answerId=1',
        ];

        yield 'no-host-existingQueryParam' => [
            'url' => '/path?answerId=2&a=b',
            'expected' => '/path?answerId=1&a=b',
        ];
    }

    /**
     * @dataProvider provideAddQueryParameter
     */
    public function testAddQueryParameter(string $url, string $expected)
    {
        $this->assertSame($expected, Url::addQueryParameter($url, 'answerId', '1'));
    }
}
