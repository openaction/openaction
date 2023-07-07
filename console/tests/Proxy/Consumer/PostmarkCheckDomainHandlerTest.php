<?php

namespace App\Tests\Proxy\Consumer;

use App\Entity\Domain;
use App\Entity\Model\CloudflareDomainConfig;
use App\Entity\Model\PostmarkDomainConfig;
use App\Proxy\Consumer\PostmarkCheckDomainHandler;
use App\Proxy\Consumer\PostmarkCheckDomainMessage;
use App\Proxy\Consumer\PostmarkConfigureDomainMessage;
use App\Tests\UnitTestCase;
use Symfony\Component\Messenger\Envelope;

class PostmarkCheckDomainHandlerTest extends UnitTestCase
{
    use DomainHandlerTestTrait;

    public function provideConsume()
    {
        yield 'not-found' => [
            'domain' => null,
            'shouldConfigure' => false,
            'shouldCheck' => false,
            'shouldTransition' => false,
        ];

        /*
         * Not provisionned
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['postmark_pending' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));

        yield 'not-provisionned' => [
            'domain' => $domain,
            'shouldConfigure' => true,
            'shouldCheck' => false,
            'shouldTransition' => false,
        ];

        /*
         * Fully verified
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['postmark_pending' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));
        $domain->setPostmarkConfig(PostmarkDomainConfig::fromConfig(['id' => 3, 'name' => $domain]));

        yield 'fully-verified' => [
            'domain' => $domain,
            'shouldConfigure' => false,
            'shouldCheck' => true,
            'shouldTransition' => true,
        ];
    }

    /**
     * @dataProvider provideConsume
     */
    public function testConsume(?Domain $domain, bool $shouldConfigure, bool $shouldCheck, bool $shouldTransition)
    {
        $this->manager->expects($this->once())
            ->method('find')
            ->with(Domain::class, 1)
            ->willReturn($domain)
        ;

        if ($shouldConfigure) {
            $this->bus->expects($this->once())->method('dispatch')->willReturn(new Envelope(new PostmarkConfigureDomainMessage(1)));
        } else {
            $this->bus->expects($this->never())->method('dispatch');
        }

        $handler = new PostmarkCheckDomainHandler(
            $this->workflow,
            $this->manager,
            $this->bus,
            $this->cloudflare,
            $this->sendgrid,
            $this->postmark,
            $this->logger
        );

        $handler(new PostmarkCheckDomainMessage(1));

        if ($shouldCheck) {
            $this->assertArrayHasKey(3, $this->postmark->checked);
        }

        if ($domain && $shouldTransition) {
            $this->assertArrayHasKey('postmark_ready', $domain->getConfigurationStatus());
        } elseif ($domain) {
            $this->assertArrayNotHasKey('postmark_ready', $domain->getConfigurationStatus());
        }
    }
}
