<?php

namespace App\Tests\Proxy\Consumer;

use App\Entity\Domain;
use App\Entity\Model\CloudflareDomainConfig;
use App\Proxy\Consumer\CloudflareProvisionDomainHandler;
use App\Proxy\Consumer\CloudflareProvisionDomainMessage;
use App\Tests\UnitTestCase;

class CloudflareProvisionDomainHandlerTest extends UnitTestCase
{
    use DomainHandlerTestTrait;

    public function provideConsume()
    {
        yield 'not-found' => [
            'domain' => null,
            'shouldProvision' => false,
            'shouldTransition' => false,
        ];

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['cloudflare_created' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));

        yield 'fully-verified' => [
            'domain' => $domain,
            'shouldProvision' => true,
            'shouldTransition' => true,
        ];
    }

    /**
     * @dataProvider provideConsume
     */
    public function testConsume(?Domain $domain, bool $shouldProvision, bool $shouldTransition)
    {
        $this->manager->expects($this->once())
            ->method('find')
            ->with(Domain::class, 1)
            ->willReturn($domain)
        ;

        $handler = new CloudflareProvisionDomainHandler(
            $this->workflow,
            $this->manager,
            $this->bus,
            $this->cloudflare,
            $this->sendgrid,
            $this->postmark,
            $this->logger
        );

        $handler(new CloudflareProvisionDomainMessage(1));

        if ($shouldProvision) {
            $this->assertArrayHasKey('149b4ece', $this->cloudflare->provisioned);
        }

        if ($domain && $shouldTransition) {
            $this->assertArrayHasKey('cloudflare_provisioned', $domain->getConfigurationStatus());
        } elseif ($domain) {
            $this->assertArrayNotHasKey('cloudflare_provisioned', $domain->getConfigurationStatus());
        }
    }
}
