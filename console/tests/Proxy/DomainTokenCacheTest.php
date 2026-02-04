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

        $tokens = static::getContainer()->get(CacheInterface::class)->get('domains-tokens', fn () => null);

        $expectedDomains = [
            'example.com',
            'exampleco.com',
            'localhost',
            'trial-62241741.c4o.io',
            'example-tag.citipo.com',
            'ile-de-france.citipo.com',
            'citipo.com',
        ];

        $this->assertNotNull($tokens);
        foreach ($expectedDomains as $domain) {
            $this->assertArrayHasKey($domain, $tokens);
            $this->assertNotEmpty($tokens[$domain]);
        }

        $actualDomains = array_keys($tokens);
        sort($actualDomains);
        sort($expectedDomains);
        $this->assertSame($expectedDomains, $actualDomains);
    }
}
