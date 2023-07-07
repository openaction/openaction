<?php

namespace App\Tests\Proxy\Consumer;

use App\Entity\Domain;
use App\Proxy\Consumer\CloudflareCreateDomainHandler;
use App\Proxy\Consumer\CloudflareCreateDomainMessage;
use App\Proxy\Consumer\CloudflareProvisionDomainMessage;
use App\Tests\UnitTestCase;
use Symfony\Component\Messenger\Envelope;

class CloudflareCreateDomainHandlerTest extends UnitTestCase
{
    use DomainHandlerTestTrait;

    public function provideConsume()
    {
        yield 'not-found' => [
            'domain' => null,
            'shouldDispatch' => false,
            'assertCallback' => static function (self $testCase) {
                $testCase->assertEmpty($testCase->cloudflare->domains);
            },
        ];

        $domain = new Domain($this->createOrganization(1), 'existingdomain.com');
        $this->setProperty($domain, 'id', 1);

        yield 'create' => [
            'domain' => $domain,
            'shouldDispatch' => true,
            'assertCallback' => static function (self $testCase, Domain $domain) {
                $testCase->assertArrayHasKey('existingdomain.com', $testCase->cloudflare->domains);
                $testCase->assertSame('1', $domain->getCloudflareConfig()->getId());
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

        $message = new CloudflareCreateDomainMessage(1);

        if ($shouldDispatch) {
            $this->bus->expects($this->once())->method('dispatch')->willReturn(new Envelope(new CloudflareProvisionDomainMessage(1)));
        } else {
            $this->bus->expects($this->never())->method('dispatch');
        }

        $handler = new CloudflareCreateDomainHandler(
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
