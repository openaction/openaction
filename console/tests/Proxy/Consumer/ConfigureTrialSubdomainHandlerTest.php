<?php

namespace App\Tests\Proxy\Consumer;

use App\Bridge\Cloudflare\CloudflareInterface;
use App\Proxy\Consumer\ConfigureTrialSubdomainHandler;
use App\Proxy\Consumer\ConfigureTrialSubdomainMessage;
use App\Tests\KernelTestCase;

class ConfigureTrialSubdomainHandlerTest extends KernelTestCase
{
    public function testConsume()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ConfigureTrialSubdomainHandler::class);
        $handler(new ConfigureTrialSubdomainMessage('trial-subdomain'));

        // The domain should have been created using the Cloudflare bridge
        $this->assertArrayHasKey('trial-subdomain', static::getContainer()->get(CloudflareInterface::class)->domains);
    }
}
