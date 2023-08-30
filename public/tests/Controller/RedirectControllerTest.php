<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RedirectControllerTest extends WebTestCase
{
    public function provideRedirect()
    {
        yield ['/_redirect/home', '/'];
        yield ['/_redirect/post/60mVI2U8pnvAOPW19MBiaW', '/posts/60mVI2U8pnvAOPW19MBiaW/redirect'];
        yield ['/_redirect/event/2ua7MEnHmxGdypDbl6fgO4', '/events/2ua7MEnHmxGdypDbl6fgO4/redirect'];
        yield ['/_redirect/page/1k8qbksfGCGNJTlcecs8nd', '/pages/1k8qbksfGCGNJTlcecs8nd/redirect'];
        yield ['/_redirect/form/3LdNrguFZQxHjHqYsYiBlr', '/forms/3LdNrguFZQxHjHqYsYiBlr/redirect'];
        yield ['/_redirect/manage-gdpr/1', '/gdpr/1'];
    }

    /**
     * @dataProvider provideRedirect
     */
    public function testRedirect(string $url, string $expectedLocation)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertResponseRedirects($expectedLocation);
    }

    public function provideShare()
    {
        yield ['/share/page/1k8qbksfGCGNJTlcecs8nd/emmanuel-macron-la-tentation-d-une-demission-reelection'];
        yield ['/share/post/12kud62vBV0tM2maNCAnl6/on-homepage'];
        yield ['/share/event/2ua7MEnHmxGdypDbl6fgO4/event-public'];
    }

    /**
     * @dataProvider provideShare
     */
    public function testShare(string $url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }
}
