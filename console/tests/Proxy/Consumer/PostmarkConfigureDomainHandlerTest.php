<?php

namespace App\Tests\Proxy\Consumer;

use App\Entity\Domain;
use App\Entity\Model\CloudflareDomainConfig;
use App\Entity\Model\PostmarkDomainConfig;
use App\Proxy\Consumer\PostmarkCheckDomainMessage;
use App\Proxy\Consumer\PostmarkConfigureDomainHandler;
use App\Proxy\Consumer\PostmarkConfigureDomainMessage;
use App\Tests\UnitTestCase;
use Symfony\Component\Messenger\Envelope;

class PostmarkConfigureDomainHandlerTest extends UnitTestCase
{
    use DomainHandlerTestTrait;

    public function provideConsume()
    {
        yield 'not-found' => [
            'domain' => null,
            'shouldDispatch' => false,
            'assertCallback' => static function (self $testCase) {
                $testCase->assertEmpty($testCase->postmark->authenticated);
            },
        ];

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);
        $domain->setConfigurationStatus(['postmark_pending' => 1]);
        $domain->setCloudflareConfig(CloudflareDomainConfig::fromConfig(['id' => '149b4ece']));
        $domain->setPostmarkConfig(PostmarkDomainConfig::fromConfig(['id' => 1, 'name' => $domain]));

        yield 'provision' => [
            'domain' => $domain,
            'shouldDispatch' => true,
            'assertCallback' => static function (self $testCase) {
                // Should have created on Cloudflare
                $testCase->assertArrayHasKey('149b4ece', $testCase->cloudflare->records);
                $testCase->assertSame(
                    [
                        '20210726220429pm._domainkey.existingdomain.com' => ['k=rsa; p=MI'],
                        'pm-bounces.existingdomain.com' => ['pm.mtasv.net'],
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

        $message = new PostmarkConfigureDomainMessage(1);

        if ($shouldDispatch) {
            $this->bus->expects($this->once())->method('dispatch')->willReturn(new Envelope(new PostmarkCheckDomainMessage(1)));
        } else {
            $this->bus->expects($this->never())->method('dispatch');
        }

        $handler = new PostmarkConfigureDomainHandler(
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
