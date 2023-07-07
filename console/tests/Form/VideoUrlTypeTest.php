<?php

namespace App\Tests\Form;

use App\Form\VideoUrlType;
use App\Tests\UnitTestCase;

class VideoUrlTypeTest extends UnitTestCase
{
    public function provideTransform()
    {
        yield 'empty' => ['identifier' => null, 'expected' => ''];
        yield 'youtube' => ['url' => 'youtube:rjb9FdVdX5I', 'expected' => 'https://www.youtube.com/watch?v=rjb9FdVdX5I'];
        yield 'facebook' => ['url' => 'facebook:rjb9FdVdX5I', 'expected' => 'https://www.facebook.com/watch/?v=rjb9FdVdX5I'];
    }

    /**
     * @dataProvider provideTransform
     */
    public function testTransform(?string $identifier, ?string $expected)
    {
        $this->assertSame($expected, (new VideoUrlType())->transform($identifier));
    }

    public function provideReverseTransform()
    {
        yield 'empty' => ['url' => '', 'expected' => null];

        yield 'youtube_normal' => ['url' => 'https://www.youtube.com/watch?v=rjb9FdVdX5I', 'expected' => 'youtube:rjb9FdVdX5I'];
        yield 'youtube_nowww' => ['url' => 'https://youtube.com/watch?v=rjb9FdVdX5I', 'expected' => 'youtube:rjb9FdVdX5I'];
        yield 'youtube_params' => ['url' => 'https://www.youtube.com/watch?list=PLZ_so1Pgq6tYkla7NBphs8aVC28KYGiSK&v=rjb9FdVdX5I&index=2&t=0s', 'expected' => 'youtube:rjb9FdVdX5I'];
        yield 'youtube_short' => ['url' => 'https://youtu.be/rjb9FdVdX5I', 'expected' => 'youtube:rjb9FdVdX5I'];

        yield 'facebook_normal' => ['url' => 'https://www.facebook.com/watch/?v=rjb9FdVdX5I', 'expected' => 'facebook:rjb9FdVdX5I'];
        yield 'facebook_nowww' => ['url' => 'https://facebook.com/watch/?v=rjb9FdVdX5I', 'expected' => 'facebook:rjb9FdVdX5I'];
        yield 'facebook_params' => ['url' => 'https://www.facebook.com/watch/?extid=NS-UNK-UNK-UNK-IOS_GK0T-GK1C&v=rjb9FdVdX5I&index=2&t=0s', 'expected' => 'facebook:rjb9FdVdX5I'];
        yield 'facebook_short' => ['url' => 'https://fb.watch/rjb9FdVdX5I/', 'expected' => 'facebook:rjb9FdVdX5I'];
    }

    /**
     * @dataProvider provideReverseTransform
     */
    public function testReverseTransform(string $url, ?string $expected)
    {
        $this->assertSame($expected, (new VideoUrlType())->reverseTransform($url));
    }
}
