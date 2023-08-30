<?php

namespace App\Tests\Controller\Membership;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function provideAuthenticatedUrls()
    {
        yield ['/members/area/dashboard'];
        yield ['/members/area/resources'];
        yield ['/members/area/resources/page/34YYmzzPB5xTrBOepv2UIl/only-for-members-page'];
        yield ['/members/area/posts'];
        yield ['/members/area/posts/2mext8M6utfwHKH2rbC7Iy/only-for-members-post'];
        yield ['/members/area/events'];
        yield ['/members/area/events/4QvbwiukHviMTFFEJvlnEO/only-for-members-event'];
        yield ['/members/area/account'];
    }

    /**
     * @dataProvider provideAuthenticatedUrls
     */
    public function testAuthenticatedUrlsRedirect(string $url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertResponseRedirects('/members/login');
    }
}
