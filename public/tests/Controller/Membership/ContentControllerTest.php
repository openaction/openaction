<?php

namespace App\Tests\Controller\Membership;

class ContentControllerTest extends AbstractMembershipControllerTest
{
    public function provideContentUrls()
    {
        yield [
            'url' => '/members/area/dashboard',
            'expectedSelector' => '.user-content:contains("Hello world")',
        ];

        yield [
            'url' => '/members/area/resources',
            'expectedSelector' => 'h5:contains("Only for members page")',
        ];

        yield [
            'url' => '/members/area/resources/page/34YYmzzPB5xTrBOepv2UIl/only-for-members-page',
            'expectedSelector' => 'h1:contains("Only for members page")',
        ];

        yield [
            'url' => '/members/area/posts',
            'expectedSelector' => 'h5:contains("Only for members post")',
        ];

        yield [
            'url' => '/members/area/posts/2mext8M6utfwHKH2rbC7Iy/only-for-members-post',
            'expectedSelector' => 'h1:contains("Only for members post")',
        ];

        yield [
            'url' => '/members/area/events',
            'expectedSelector' => 'h5:contains("Only for members event")',
        ];

        yield [
            'url' => '/members/area/events/4QvbwiukHviMTFFEJvlnEO/only-for-members-event',
            'expectedSelector' => 'h1:contains("Only for members event")',
        ];
    }

    /**
     * @dataProvider provideContentUrls
     */
    public function testContentUrls(string $url, string $expectedSelector)
    {
        $client = self::createClient();
        $this->authenticate($client);

        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists($expectedSelector);
    }
}
