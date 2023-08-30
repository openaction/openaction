<?php

namespace App\Tests\Analytics;

use Analytics\PageViewHandler;
use Analytics\PageViewPersister;
use App\Tests\UnitTestCase;
use App\Util\Json;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class PageViewHandlerTest extends UnitTestCase
{
    public function provideValidPageView()
    {
        yield 'basic' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'u' => 'https://citipo.com/fr/blog',
                'r' => 'https://twitter.com',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '82.65.40.45',
            ],
            'expected' => [
                'projectUuid' => '2c720420-65fd-4360-9d77-731758008497',
                'ip' => '82.65.40.45',
                'path' => '/fr/blog',
                'platform' => 'Linux',
                'browser' => 'Firefox',
                'country' => 'fr',
                'referrer' => 'twitter.com',
                'referrerPath' => null,
                'utmSource' => null,
                'utmMedium' => null,
                'utmCampaign' => null,
                'utmContent' => null,
            ],
        ];

        yield 'utm' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'u' => 'https://citipo.com/fr/blog',
                'r' => 'https://twitter.com',
                'uso' => 'twitter',
                'ume' => 'email',
                'uca' => '25',
                'uco' => 'example',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '82.65.40.45',
            ],
            'expected' => [
                'projectUuid' => '2c720420-65fd-4360-9d77-731758008497',
                'ip' => '82.65.40.45',
                'path' => '/fr/blog',
                'platform' => 'Linux',
                'browser' => 'Firefox',
                'country' => 'fr',
                'referrer' => 'twitter.com',
                'referrerPath' => null,
                'utmSource' => 'twitter',
                'utmMedium' => 'email',
                'utmCampaign' => '25',
                'utmContent' => 'example',
            ],
        ];

        yield 'ipv6' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'u' => 'https://citipo.com',
                'r' => 'https://twitter.com',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '2a06:98c0:3600:0:0:0:0:103',
            ],
            'expected' => [
                'projectUuid' => '2c720420-65fd-4360-9d77-731758008497',
                'ip' => '2a06:98c0:3600:0:0:0:0:103',
                'path' => '/',
                'platform' => 'Linux',
                'browser' => 'Firefox',
                'country' => 'fr',
                'referrer' => 'twitter.com',
                'referrerPath' => null,
                'utmSource' => null,
                'utmMedium' => null,
                'utmCampaign' => null,
                'utmContent' => null,
            ],
        ];

        yield 'no-ua' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'u' => 'https://citipo.com',
                'r' => 'https://twitter.com/path',
            ],
            'headers' => [
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '82.65.40.45',
            ],
            'expected' => [
                'projectUuid' => '2c720420-65fd-4360-9d77-731758008497',
                'ip' => '82.65.40.45',
                'path' => '/',
                'platform' => null,
                'browser' => null,
                'country' => 'fr',
                'referrer' => 'twitter.com',
                'referrerPath' => '/path',
                'utmSource' => null,
                'utmMedium' => null,
                'utmCampaign' => null,
                'utmContent' => null,
            ],
        ];

        yield 'no-referrer' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'u' => 'https://citipo.com/fr/blog',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '82.65.40.45',
            ],
            'expected' => [
                'projectUuid' => '2c720420-65fd-4360-9d77-731758008497',
                'ip' => '82.65.40.45',
                'path' => '/fr/blog',
                'platform' => 'Linux',
                'browser' => 'Firefox',
                'country' => 'fr',
                'referrer' => null,
                'referrerPath' => null,
                'utmSource' => null,
                'utmMedium' => null,
                'utmCampaign' => null,
                'utmContent' => null,
            ],
        ];

        yield 'no-cloudflare' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'u' => 'https://citipo.com/fr/blog',
                'r' => 'https://twitter.com',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
            ],
            'expected' => [
                'projectUuid' => '2c720420-65fd-4360-9d77-731758008497',
                'ip' => '127.0.0.1',
                'path' => '/fr/blog',
                'platform' => 'Linux',
                'browser' => 'Firefox',
                'country' => null,
                'referrer' => 'twitter.com',
                'referrerPath' => null,
                'utmSource' => null,
                'utmMedium' => null,
                'utmCampaign' => null,
                'utmContent' => null,
            ],
        ];

        yield 'long-path' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'u' => 'https://citipo.com/fr/'.str_repeat('a', 300),
                'r' => 'https://twitter.com/'.str_repeat('a', 300),
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
            ],
            'expected' => [
                'projectUuid' => '2c720420-65fd-4360-9d77-731758008497',
                'ip' => '127.0.0.1',
                'path' => '/fr/'.str_repeat('a', 246),
                'platform' => 'Linux',
                'browser' => 'Firefox',
                'country' => null,
                'referrer' => 'twitter.com',
                'referrerPath' => '/'.str_repeat('a', 249),
                'utmSource' => null,
                'utmMedium' => null,
                'utmCampaign' => null,
                'utmContent' => null,
            ],
        ];
    }

    /**
     * @dataProvider provideValidPageView
     */
    public function testValidPageView(array $payload, array $headers, array $expected)
    {
        $payload['n'] = 'pageview';

        $request = new Request([], [], [], [], [], [], Json::encode($payload));
        $request->headers = new HeaderBag($headers);

        /** @var PageViewPersister|MockObject $persister */
        $persister = $this->createMock(PageViewPersister::class);
        $persister->expects($this->once())
            ->method('persist')
            ->with(...array_values($expected))
        ;

        $response = (new PageViewHandler($persister))->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
        $this->assertSame('must-revalidate, no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }

    public function provideInvalidPageView()
    {
        yield 'no-id' => [
            'query' => [
                'u' => 'https://citipo.com/fr/blog',
                'r' => 'https://twitter.com',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '82.65.40.45',
            ],
        ];

        yield 'invalid-id' => [
            'query' => [
                'p' => '',
                'u' => 'https://citipo.com/fr/blog',
                'r' => 'https://twitter.com',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '82.65.40.45',
            ],
        ];

        yield 'no-url' => [
            'query' => [
                'p' => '1LrlbnyOoMgF3wqx0hrobH',
                'r' => 'https://twitter.com',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
                'CF-IPCountry' => 'FR',
                'CF-Connecting-IP' => '82.65.40.45',
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidPageView
     */
    public function testInvalidPageView(array $query, array $headers)
    {
        $request = new Request($query);
        $request->headers = new HeaderBag($headers);

        /** @var PageViewPersister|MockObject $persister */
        $persister = $this->createMock(PageViewPersister::class);
        $persister->expects($this->never())->method('persist');

        $response = (new PageViewHandler($persister))->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
        $this->assertSame('must-revalidate, no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }

    public function testContentView()
    {
        $request = new Request([], [], [], [], [], [], Json::encode([
            'n' => 'contentview',
            'p' => '1LrlbnyOoMgF3wqx0hrobH',
            'u' => 'https://citipo.com/fr/',
            'r' => 'https://twitter.com/',
            'm' => [
                'type' => 'post',
                'id' => '60mVI2U8pnvAOPW19MBiaW',
            ],
        ]));

        $request->headers = new HeaderBag([
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
        ]);

        /** @var PageViewPersister|MockObject $persister */
        $persister = $this->createMock(PageViewPersister::class);
        $persister->expects($this->once())
            ->method('incrementPageViews')
            ->with('post', 'c58e4465-0168-568e-a6fd-1ed44f555bb0')
        ;

        $response = (new PageViewHandler($persister))->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
        $this->assertSame('must-revalidate, no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }

    public function testCustomEvent()
    {
        $request = new Request([], [], [], [], [], [], Json::encode([
            'n' => 'customevent',
            'p' => '1LrlbnyOoMgF3wqx0hrobH',
            'm' => ['event' => 'level_1'],
        ]));

        /** @var PageViewPersister|MockObject $persister */
        $persister = $this->createMock(PageViewPersister::class);
        $persister->expects($this->once())
            ->method('persistEvent')
            ->with('2c720420-65fd-4360-9d77-731758008497', '127.0.0.1', 'level_1')
        ;

        $response = (new PageViewHandler($persister))->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
        $this->assertSame('must-revalidate, no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }
}
