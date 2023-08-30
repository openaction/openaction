<?php

namespace App\Tests\Controller\Shareable;

use App\Tests\WebTestCase;

class EmailingControllerTest extends WebTestCase
{
    public function provideShareablePages()
    {
        yield ['/shareable/dr2UodGsKnDo8ewJvox3X/emailing'];
        yield ['/shareable/dr2UodGsKnDo8ewJvox3X/emailing/CcIlYi8lMpZZj3nlPYgCl'];
    }

    /**
     * @dataProvider provideShareablePages
     */
    public function testShareablePages(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    public function provideShareableNotFound()
    {
        yield ['/shareable/invalid/emailing'];
        yield ['/shareable/dr2UodGsKnDo8ewJvox3X/emailing/invalid'];
    }

    /**
     * @dataProvider provideShareableNotFound
     */
    public function testShareableNotFound(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(404);
    }
}
