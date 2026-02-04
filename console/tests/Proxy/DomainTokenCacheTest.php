<?php

namespace App\Tests\Proxy;

use App\Proxy\DomainTokenCache;
use App\Tests\KernelTestCase;
use Symfony\Contracts\Cache\CacheInterface;

class DomainTokenCacheTest extends KernelTestCase
{
    public function testRefresh()
    {
        self::bootKernel();

        static::getContainer()->get(DomainTokenCache::class)->refresh();

        $this->assertSame(
            [
                'example.com' => 'a162d719a9106a1e77449a0c8079626b9eeb76e381eeb80041b945030d38a06f',
                'exampleco.com' => '41d7821176ed9079640650922e1290aba97b949362339a7ed5539f0d5b9f21ba',
                'localhost' => '31cf08f5e0354198a3b26b5b08f59a4ed871cbaec6e4eb8b158fab57a7193b7a',
                'trial-62241741.c4o.io' => 'ed17609e4cbcfc2af23df24a996a410ec197d7877006b4d382275b7b63a0a713',
                'example-tag.citipo.com' => '07c407b64f18e23d6f726894fc0cc79f4f3a4683898cdd75936c94475d55049c',
                'ile-de-france.citipo.com' => '3a4683898cdd75936c94475d55049c07c407b64f18e23d6f726894fc0cc79f4f',
                'citipo.com' => '748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad',
            ],
            static::getContainer()->get(CacheInterface::class)->get('domains-tokens', fn () => null),
        );
    }
}
