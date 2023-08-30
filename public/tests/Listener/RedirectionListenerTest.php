<?php

namespace App\Tests\Listener;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RedirectionListenerTest extends WebTestCase
{
    public function provideRedirection()
    {
        yield ['/redirection/static', '/redirection-2-target', 302];
        yield ['/redirection/dynamic/variable/foo', '/redirection/variable/1-target', 301];
        yield ['/redirection/none', null, 404];
    }

    /**
     * @dataProvider provideRedirection
     */
    public function testRedirection(string $source, ?string $expectedTarget, int $expectedCode)
    {
        $client = self::createClient();
        $client->request('GET', $source);
        $this->assertResponseStatusCodeSame($expectedCode);

        if ($expectedTarget) {
            $this->assertResponseRedirects($expectedTarget);
        }
    }
}
