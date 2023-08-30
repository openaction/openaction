<?php

namespace App\Tests\Proxy;

use App\Entity\Domain;
use App\Entity\Organization;
use App\Entity\Project;
use App\Proxy\Consumer\CloudflareCreateDomainMessage;
use App\Proxy\Consumer\ConfigureTrialSubdomainMessage;
use App\Proxy\DomainManager;
use App\Repository\DomainRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;

class DomainManagerTest extends KernelTestCase
{
    public function testGetTrialDomain()
    {
        self::bootKernel();

        $this->assertSame('c4o.io', $this->getDomainManager()->getTrialDomain()->getName());
    }

    public function testGenerateTrialSubdomain()
    {
        self::bootKernel();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Citipo']);

        $subdomain = $this->getDomainManager()->generateTrialSubdomain($project);
        $this->assertSame('citipo-e816', $subdomain);
    }

    public function testConnectTrialSubdomain()
    {
        self::bootKernel();

        $this->getDomainManager()->connectTrialSubdomain('trial-subdomain');

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var ConfigureTrialSubdomainMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(ConfigureTrialSubdomainMessage::class, $message);
        $this->assertSame('trial-subdomain', $message->getSubdomain());
    }

    public function testCreateDomain()
    {
        self::bootKernel();

        $this->assertNull(static::getContainer()->get(DomainRepository::class)->findOneBy(['name' => 'created.com']));

        /** @var Organization $organization */
        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['name' => 'Citipo']);

        $this->getDomainManager()->createDomain($organization, 'created.com');

        /** @var Domain $domain */
        $domain = static::getContainer()->get(DomainRepository::class)->findOneBy(['name' => 'created.com']);
        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertSame('created.com', $domain->getName());
        $this->assertSame($organization->getId(), $domain->getOrganization()->getId());
        $this->assertSame(['cloudflare_pending' => 1], $domain->getConfigurationStatus());

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var CloudflareCreateDomainMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(CloudflareCreateDomainMessage::class, $message);
        $this->assertSame($domain->getId(), $message->getDomainId());
    }

    private function getDomainManager(): DomainManager
    {
        return static::getContainer()->get(DomainManager::class);
    }
}
