<?php

namespace App\Tests\Util;

use App\Tests\UnitTestCase;
use App\Util\Video;

class VideoTest extends UnitTestCase
{
    public function provideFromReference(): iterable
    {
        yield 'empty' => ['reference' => null, 'expected' => null];
        yield 'invalid' => ['reference' => 'invalid:1', 'expected' => null];
        yield 'youtube' => ['reference' => 'youtube:rjb9FdVdX5I', 'expected' => new Video('youtube', 'rjb9FdVdX5I')];
        yield 'facebook' => ['reference' => 'facebook:rjb9FdVdX5I', 'expected' => new Video('facebook', 'rjb9FdVdX5I')];
    }

    /**
     * @dataProvider provideFromReference
     */
    public function testFromReference(?string $reference, ?Video $expected): void
    {
        if (!$expected) {
            $this->assertNull(Video::fromReference($reference));
        } else {
            $video = Video::fromReference($reference);
            $this->assertSame($expected->getProvider(), $video->getProvider());
            $this->assertSame($expected->getId(), $video->getId());
        }
    }

    public function provideCreateFromUrl(): iterable
    {
        yield 'empty' => ['url' => '', 'expected' => null];

        yield 'youtube_normal' => ['url' => 'https://www.youtube.com/watch?v=rjb9FdVdX5I', 'expected' => new Video('youtube', 'rjb9FdVdX5I')];
        yield 'youtube_nowww' => ['url' => 'https://youtube.com/watch?v=rjb9FdVdX5I', 'expected' => new Video('youtube', 'rjb9FdVdX5I')];
        yield 'youtube_params' => ['url' => 'https://www.youtube.com/watch?list=PLZ_so1Pgq6tYkla7NBphs8aVC28KYGiSK&v=rjb9FdVdX5I&index=2&t=0s', 'expected' => new Video('youtube', 'rjb9FdVdX5I')];
        yield 'youtube_short' => ['url' => 'https://youtu.be/rjb9FdVdX5I', 'expected' => new Video('youtube', 'rjb9FdVdX5I')];

        yield 'facebook_normal' => ['url' => 'https://www.facebook.com/watch/?v=rjb9FdVdX5I', 'expected' => new Video('facebook', 'rjb9FdVdX5I')];
        yield 'facebook_nowww' => ['url' => 'https://facebook.com/watch/?v=rjb9FdVdX5I', 'expected' => new Video('facebook', 'rjb9FdVdX5I')];
        yield 'facebook_params' => ['url' => 'https://www.facebook.com/watch/?extid=NS-UNK-UNK-UNK-IOS_GK0T-GK1C&v=rjb9FdVdX5I&index=2&t=0s', 'expected' => new Video('facebook', 'rjb9FdVdX5I')];
        yield 'facebook_short' => ['url' => 'https://fb.watch/rjb9FdVdX5I/', 'expected' => new Video('facebook', 'rjb9FdVdX5I')];
    }

    /**
     * @dataProvider provideCreateFromUrl
     */
    public function testCreateFromUrl(string $url, ?Video $expected): void
    {
        if (!$expected) {
            $this->assertNull(Video::createFromUrl($url));
        } else {
            $video = Video::createFromUrl($url);
            $this->assertSame($expected->getProvider(), $video->getProvider());
            $this->assertSame($expected->getId(), $video->getId());
        }
    }

    public function provideToProviderUrl(): iterable
    {
        yield 'youtube' => ['video' => new Video('youtube', 'rjb9FdVdX5I'), 'expected' => 'https://www.youtube.com/watch?v=rjb9FdVdX5I'];
        yield 'facebook' => ['video' => new Video('facebook', 'rjb9FdVdX5I'), 'expected' => 'https://www.facebook.com/watch/?v=rjb9FdVdX5I'];
    }

    /**
     * @dataProvider provideToProviderUrl
     */
    public function testToProviderUrl(Video $video, string $expected): void
    {
        $this->assertSame($expected, $video->toProviderUrl());
    }

    public function provideToReference(): iterable
    {
        yield 'youtube' => ['video' => new Video('youtube', 'rjb9FdVdX5I'), 'expected' => 'youtube:rjb9FdVdX5I'];
        yield 'facebook' => ['video' => new Video('facebook', 'rjb9FdVdX5I'), 'expected' => 'facebook:rjb9FdVdX5I'];
    }

    /**
     * @dataProvider provideToReference
     */
    public function testToReference(Video $video, string $expected): void
    {
        $this->assertSame($expected, $video->toReference());
    }
}
