<?php

namespace App\Tests\Controller\Console;

use App\Entity\UserVisit;
use App\Repository\UserVisitRepository;
use App\Tests\WebTestCase;

class AnnouncementControllerTest extends WebTestCase
{
    public function testSeeAnnouncements()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        static::getContainer()->get(UserVisitRepository::class)->createQueryBuilder('v')->delete()->getQuery()->execute();

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/projects');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('[data-user]:contains("Titouan Galopin")'));
        $this->assertCount(1, $crawler->filter('[data-controller="whats-new"]'));
        $this->assertCount(1, $crawler->filter('.list-group-item:contains("Améliorations de la gestion des images")'));
        $this->assertCount(1, $crawler->filter('.btn:contains("Vérifier mes images de candidats")'));

        // Check the visit was tracked
        $this->assertSame(1, static::getContainer()->get(UserVisitRepository::class)->count([]));

        /** @var UserVisit $visit */
        $visit = static::getContainer()->get(UserVisitRepository::class)->findOneBy([]);
        $this->assertSame('titouan.galopin@citipo.com', $visit->getOwner()->getEmail());
        $this->assertSame(1, $visit->getPageViews());
        $this->assertInstanceOf(\DateTime::class, $visit->getDate());
    }
}
