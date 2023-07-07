<?php

namespace App\Tests\Proxy\Consumer;

use App\Entity\Domain;
use App\Entity\Model\CloudflareDomainConfig;
use App\Entity\Model\SendgridDomainConfig;
use App\Proxy\Consumer\SendgridCheckDomainHandler;
use App\Proxy\Consumer\SendgridCheckDomainMessage;
use App\Proxy\Consumer\SendgridConfigureDomainMessage;
use App\Tests\UnitTestCase;
use Symfony\Component\Messenger\Envelope;

class SendgridCheckDomainHandlerTest extends UnitTestCase
{
    use DomainHandlerTestTrait;

    public function provideConsume()
    {
        yield 'not-found' => [
            'domain' => null,
            'sendgridValid' => false,
            'shouldConfigure' => false,
            'shouldRequestCheck' => false,
            'shouldTransition' => false,
        ];

        /*
         * To create
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['sendgrid_domain_pending' => 1, 'sendgrid_branded_link_pending' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig(['id' => 4, 'brandedLinkId' => 5]));

        yield 'to-create' => [
            'domain' => $domain,
            'sendgridValid' => false,
            'shouldConfigure' => true,
            'shouldRequestCheck' => false,
            'shouldTransition' => false,
        ];

        /*
         * To provision
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['sendgrid_domain_created' => 1, 'sendgrid_branded_link_created' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig(['id' => 4, 'brandedLinkId' => 5]));

        yield 'to-provision' => [
            'domain' => $domain,
            'sendgridValid' => false,
            'shouldConfigure' => true,
            'shouldRequestCheck' => false,
            'shouldTransition' => false,
        ];

        /*
         * Already valid in database
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['sendgrid_provisioned' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig([
            'id' => 4,
            'domain' => 'existingdomain.com',
            'subdomain' => 'mail',
            'valid' => true,
            'mailCnameHost' => 'mail.existingdomain.com',
            'mailCnameTarget' => 'sendgrid.net',
            'dkim1CnameHost' => 's1._domainkey.existingdomain.com',
            'dkim1CnameTarget' => 's1.domainkey.u17150189.wl190.sendgrid.net',
            'dkim2CnameHost' => 's2._domainkey.existingdomain.com',
            'dkim2CnameTarget' => 's2.domainkey.u17150189.wl190.sendgrid.net',
            'brandedLinkId' => 5,
            'brandedLinkValid' => true,
            'brandedLinkDomainCnameHost' => 'mail.existingdomain.com',
            'brandedLinkDomainCnameTarget' => 'sendgrid.net',
            'brandedLinkOwnerCnameHost' => '17150189.existingdomain.com',
            'brandedLinkOwnerCnameTarget' => 'sendgrid.net',
        ]));

        yield 'already-valid' => [
            'domain' => $domain,
            'sendgridValid' => true,
            'shouldConfigure' => false,
            'shouldRequestCheck' => false,
            'shouldTransition' => true,
        ];

        /*
         * Updated as valid
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['sendgrid_provisioned' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig([
            'id' => 4,
            'domain' => 'existingdomain.com',
            'subdomain' => 'mail',
            'valid' => false,
            'mailCnameHost' => 'mail.existingdomain.com',
            'mailCnameTarget' => 'sendgrid.net',
            'dkim1CnameHost' => 's1._domainkey.existingdomain.com',
            'dkim1CnameTarget' => 's1.domainkey.u17150189.wl190.sendgrid.net',
            'dkim2CnameHost' => 's2._domainkey.existingdomain.com',
            'dkim2CnameTarget' => 's2.domainkey.u17150189.wl190.sendgrid.net',
            'brandedLinkId' => 5,
            'brandedLinkValid' => false,
            'brandedLinkDomainCnameHost' => 'mail.existingdomain.com',
            'brandedLinkDomainCnameTarget' => 'sendgrid.net',
            'brandedLinkOwnerCnameHost' => '17150189.existingdomain.com',
            'brandedLinkOwnerCnameTarget' => 'sendgrid.net',
        ]));

        yield 'updated-valid' => [
            'domain' => $domain,
            'sendgridValid' => true,
            'shouldConfigure' => false,
            'shouldRequestCheck' => false,
            'shouldTransition' => true,
        ];

        /*
         * Request new check
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['sendgrid_provisioned' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig([
            'id' => 4,
            'domain' => 'existingdomain.com',
            'subdomain' => 'mail',
            'valid' => false,
            'mailCnameHost' => 'mail.existingdomain.com',
            'mailCnameTarget' => 'sendgrid.net',
            'dkim1CnameHost' => 's1._domainkey.existingdomain.com',
            'dkim1CnameTarget' => 's1.domainkey.u17150189.wl190.sendgrid.net',
            'dkim2CnameHost' => 's2._domainkey.existingdomain.com',
            'dkim2CnameTarget' => 's2.domainkey.u17150189.wl190.sendgrid.net',
            'brandedLinkId' => 5,
            'brandedLinkValid' => false,
            'brandedLinkDomainCnameHost' => 'mail.existingdomain.com',
            'brandedLinkDomainCnameTarget' => 'sendgrid.net',
            'brandedLinkOwnerCnameHost' => '17150189.existingdomain.com',
            'brandedLinkOwnerCnameTarget' => 'sendgrid.net',
        ]));

        yield 'request-check' => [
            'domain' => $domain,
            'sendgridValid' => false,
            'shouldConfigure' => false,
            'shouldRequestCheck' => true,
            'shouldTransition' => false,
        ];
    }

    /**
     * @dataProvider provideConsume
     */
    public function testConsume(?Domain $domain, bool $sendgridValid, bool $shouldConfigure, bool $shouldRequestCheck, bool $shouldTransition)
    {
        $this->manager->expects($this->once())
            ->method('find')
            ->with(Domain::class, 1)
            ->willReturn($domain)
        ;

        $this->sendgrid->createDomain('existingdomain.com');
        $this->sendgrid->domains['existingdomain.com']['id'] = 4;
        $this->sendgrid->domains['existingdomain.com']['valid'] = $sendgridValid;

        $this->sendgrid->createBrandedLink('existingdomain.com');
        $this->sendgrid->brandedLinks['existingdomain.com']['id'] = 5;
        $this->sendgrid->brandedLinks['existingdomain.com']['valid'] = $sendgridValid;

        if ($shouldConfigure) {
            $this->bus->expects($this->once())->method('dispatch')->willReturn(new Envelope(new SendgridConfigureDomainMessage(1)));
        } else {
            $this->bus->expects($this->never())->method('dispatch');
        }

        $handler = new SendgridCheckDomainHandler(
            $this->workflow,
            $this->manager,
            $this->bus,
            $this->cloudflare,
            $this->sendgrid,
            $this->postmark,
            $this->logger
        );

        $handler(new SendgridCheckDomainMessage(1));

        if ($shouldRequestCheck) {
            $this->assertArrayHasKey(4, $this->sendgrid->domainValidationRequests);
            $this->assertArrayHasKey(5, $this->sendgrid->brandedLinkValidationRequests);
        }

        if ($domain && $shouldTransition) {
            $this->assertArrayHasKey('sendgrid_ready', $domain->getConfigurationStatus());
        } elseif ($domain) {
            $this->assertArrayNotHasKey('sendgrid_ready', $domain->getConfigurationStatus());
        }
    }
}
