<?php

namespace App\Tests\Proxy\Consumer;

use App\Entity\Domain;
use App\Entity\Model\CloudflareDomainConfig;
use App\Entity\Model\SendgridDomainConfig;
use App\Proxy\Consumer\SendgridConfigureDomainHandler;
use App\Proxy\Consumer\SendgridConfigureDomainMessage;
use App\Tests\UnitTestCase;
use Symfony\Component\Messenger\Envelope;

class SendgridConfigureDomainHandlerTest extends UnitTestCase
{
    use DomainHandlerTestTrait;

    public function provideConsume()
    {
        yield 'not-found' => [
            'domain' => null,
            'dispatch' => false,
            'assertCallback' => static function (self $testCase) {
                $testCase->assertCount(1, $testCase->sendgrid->domains);
            },
        ];

        /*
         * Domain
         */

        $domain = new Domain($this->createOrganization(1), 'example.com');
        $domain->setConfigurationStatus(['cloudflare_ready' => 1, 'sendgrid_domain_pending' => 1]);

        yield 'create-domain' => [
            'domain' => $domain,
            'dispatch' => false,
            'assertCallback' => static function (self $testCase, ?Domain $domain) {
                $testCase->assertArrayHasKey('example.com', $testCase->sendgrid->domains);
                $testCase->assertSame(1, $domain->getSendgridConfig()->getId());
                $testCase->assertNull($domain->getSendgridConfig()->getBrandedLinkId());
            },
        ];

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $domain->setConfigurationStatus(['cloudflare_ready' => 1, 'sendgrid_domain_pending' => 1]);

        yield 'match-existing-domain' => [
            'domain' => $domain,
            'dispatch' => false,
            'assertCallback' => static function (self $testCase, ?Domain $domain) {
                $testCase->assertArrayHasKey('existingdomain.com', $testCase->sendgrid->domains);
                $testCase->assertSame(4, $domain->getSendgridConfig()->getId());
                $testCase->assertNull($domain->getSendgridConfig()->getBrandedLinkId());
            },
        ];

        /*
         * Branded link
         */

        $domain = new Domain($this->createOrganization(1), 'example.com');
        $domain->setConfigurationStatus(['cloudflare_ready' => 1, 'sendgrid_domain_created' => 1, 'sendgrid_branded_link_pending' => 1]);
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig(['id' => 1]));

        yield 'create-branded-link' => [
            'domain' => $domain,
            'dispatch' => false,
            'assertCallback' => static function (self $testCase, ?Domain $domain) {
                $testCase->assertArrayHasKey('example.com', $testCase->sendgrid->brandedLinks);
                $testCase->assertSame(2, $domain->getSendgridConfig()->getBrandedLinkId());
            },
        ];

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $domain->setConfigurationStatus(['cloudflare_ready' => 1, 'sendgrid_domain_created' => 1, 'sendgrid_branded_link_pending' => 1]);
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig(['id' => 1]));

        yield 'match-existing-branded-link' => [
            'domain' => $domain,
            'dispatch' => false,
            'assertCallback' => static function (self $testCase, ?Domain $domain) {
                $testCase->assertArrayHasKey('existingdomain.com', $testCase->sendgrid->brandedLinks);
                $testCase->assertSame(5, $domain->getSendgridConfig()->getBrandedLinkId());
            },
        ];

        /*
         * Branded link and domain linked, load config
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $domain->setConfigurationStatus(['cloudflare_ready' => 1, 'sendgrid_domain_created' => 1, 'sendgrid_branded_link_created' => 1]);
        $domain->setSendgridConfig(SendgridDomainConfig::fromConfig(['id' => 4, 'brandedLinkId' => 5]));

        yield 'load-sendgrid-config' => [
            'domain' => $domain,
            'dispatch' => false,
            'assertCallback' => static function (self $testCase, ?Domain $domain) {
                $config = $domain->getSendgridConfig();
                $testCase->assertSame(4, $config->getId());
                $testCase->assertSame('existingdomain.com', $config->getDomain());
                $testCase->assertSame('mail', $config->getSubdomain());
                $testCase->assertFalse($config->isDomainAuthValid());
                $testCase->assertSame('mail.existingdomain.com', $config->getMailCnameHost());
                $testCase->assertSame('sendgrid.net', $config->getMailCnameTarget());
                $testCase->assertSame('s1._domainkey.existingdomain.com', $config->getDkim1CnameHost());
                $testCase->assertSame('s1.domainkey.u17150189.wl190.sendgrid.net', $config->getDkim1CnameTarget());
                $testCase->assertSame('s2._domainkey.existingdomain.com', $config->getDkim2CnameHost());
                $testCase->assertSame('s2.domainkey.u17150189.wl190.sendgrid.net', $config->getDkim2CnameTarget());
                $testCase->assertSame(5, $config->getBrandedLinkId());
                $testCase->assertFalse($config->isBrandedLinkValid());
                $testCase->assertSame('mail.existingdomain.com', $config->getBrandedLinkDomainCnameHost());
                $testCase->assertSame('sendgrid.net', $config->getBrandedLinkDomainCnameTarget());
                $testCase->assertSame('17150189.existingdomain.com', $config->getBrandedLinkOwnerCnameHost());
                $testCase->assertSame('sendgrid.net', $config->getBrandedLinkOwnerCnameTarget());
            },
        ];

        /*
         * Everything ready, provision
         */

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['cloudflare_ready' => 1, 'sendgrid_domain_created' => 1, 'sendgrid_branded_link_created' => 1]);
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

        yield 'provision' => [
            'domain' => $domain,
            'dispatch' => true,
            'assertCallback' => static function (self $testCase) {
                // Should have created on Cloudflare
                $testCase->assertArrayHasKey('149b4ece', $testCase->cloudflare->records);
                $testCase->assertSame(
                    [
                        'mail.existingdomain.com' => ['sendgrid.net', 'sendgrid.net'],
                        's1._domainkey.existingdomain.com' => ['s1.domainkey.u17150189.wl190.sendgrid.net'],
                        's2._domainkey.existingdomain.com' => ['s2.domainkey.u17150189.wl190.sendgrid.net'],
                        '17150189.existingdomain.com' => ['sendgrid.net'],
                    ],
                    $testCase->cloudflare->records['149b4ece'],
                );
            },
        ];
    }

    /**
     * @dataProvider provideConsume
     */
    public function testConsume(?Domain $domain, bool $shouldDispatch, callable $assertCallback)
    {
        $this->manager->expects($this->once())
            ->method('find')
            ->with(Domain::class, 1)
            ->willReturn($domain)
        ;

        $this->sendgrid->createDomain('existingdomain.com');
        $this->sendgrid->domains['existingdomain.com']['id'] = 4;

        $this->sendgrid->createBrandedLink('existingdomain.com');
        $this->sendgrid->brandedLinks['existingdomain.com']['id'] = 5;

        $message = new SendgridConfigureDomainMessage(1);

        if ($shouldDispatch) {
            $this->bus->expects($this->once())->method('dispatch')->willReturn(new Envelope($message));
        } else {
            $this->bus->expects($this->never())->method('dispatch');
        }

        $handler = new SendgridConfigureDomainHandler(
            $this->workflow,
            $this->manager,
            $this->bus,
            $this->cloudflare,
            $this->sendgrid,
            $this->postmark,
            $this->logger
        );

        $handler($message);

        $assertCallback($this, $domain);
    }
}
