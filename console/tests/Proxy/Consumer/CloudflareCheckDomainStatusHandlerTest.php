<?php

namespace App\Tests\Proxy\Consumer;

use App\Entity\Domain;
use App\Entity\Model\CloudflareDomainConfig;
use App\Proxy\Consumer\CloudflareCheckDomainHandler;
use App\Proxy\Consumer\CloudflareCheckDomainMessage;
use App\Proxy\Consumer\SendgridConfigureDomainMessage;
use App\Tests\UnitTestCase;
use Symfony\Component\Messenger\Envelope;

class CloudflareCheckDomainStatusHandlerTest extends UnitTestCase
{
    use DomainHandlerTestTrait;

    public function provideConsume()
    {
        yield 'not-found' => [
            'domain' => null,
            'shouldCheck' => false,
            'shouldTransition' => false,
            'shouldConfigure' => false,
        ];

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['cloudflare_provisioned' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));

        yield 'fully-verified' => [
            'domain' => $domain,
            'shouldCheck' => true,
            'shouldTransition' => true,
            'shouldConfigure' => true,
        ];
    }

    /**
     * @dataProvider provideConsume
     */
    public function testConsume(?Domain $domain, bool $shouldCheck, bool $shouldTransition, bool $shouldConfigure)
    {
        $this->manager->expects($this->once())
            ->method('find')
            ->with(Domain::class, 1)
            ->willReturn($domain)
        ;

        if ($shouldConfigure) {
            $this->bus->expects($this->exactly(2))->method('dispatch')->willReturn(new Envelope(new SendgridConfigureDomainMessage(1)));
        } else {
            $this->bus->expects($this->never())->method('dispatch');
        }

        $handler = new CloudflareCheckDomainHandler(
            $this->workflow,
            $this->manager,
            $this->bus,
            $this->cloudflare,
            $this->sendgrid,
            $this->postmark,
            $this->logger
        );

        $handler(new CloudflareCheckDomainMessage(1));

        if ($shouldCheck) {
            $this->assertArrayHasKey('149b4ece', $this->cloudflare->checked);
        }

        if ($domain && $shouldTransition) {
            $this->assertArrayHasKey('cloudflare_ready', $domain->getConfigurationStatus());
        } elseif ($domain) {
            $this->assertArrayNotHasKey('cloudflare_ready', $domain->getConfigurationStatus());
        }
    }
}
