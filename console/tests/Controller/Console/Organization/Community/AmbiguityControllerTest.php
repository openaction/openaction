<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Repository\Community\AmbiguityRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;

class AmbiguityControllerTest extends WebTestCase
{
    private const ORGANIZATION_UUID = '682746ea-3e2f-4e5b-983b-6548258a2033';

    public function testIndex(): void
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGANIZATION_UUID.'/community/ambiguities');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('div:contains("another@one.com")');
        $this->assertSelectorExists('div:contains("something@else.com")');
        $this->assertCount(2, $crawler->filter('.community-ambiguity'));
    }

    public function testMerge(): void
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGANIZATION_UUID.'/community/ambiguities');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.community-ambiguity'));

        $client->clickLink('Merge (with john@lennon.com as main email)');
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testIgnore(): void
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGANIZATION_UUID.'/community/ambiguities');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.community-ambiguity'));

        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGANIZATION_UUID);
        $qb = self::getContainer()->get(AmbiguityRepository::class)->createQueryBuilder('a');
        $queryCount = $qb
            ->select('COUNT(a.id)')
            ->where('a.organization = :organization')
            ->setParameter('organization', $orga->getId())
            ->andWhere($qb->expr()->isNotNull('a.ignoredAt'))
            ->getQuery()
        ;

        $this->assertSame(0, $queryCount->getSingleScalarResult());

        $client->clickLink('This detection is a false positive');
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSame(1, $queryCount->getSingleScalarResult());
        $this->assertCount(1, $crawler->filter('.community-ambiguity'));
    }
}
