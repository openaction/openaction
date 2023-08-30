<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Community\ImportExport\Consumer\ExportMessage;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;

class ExportControllerTest extends WebTestCase
{
    public function testExportFull()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts');
        $client->clickLink('Export contacts');

        // Should have published message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var ExportMessage $message */
        $this->assertInstanceOf(ExportMessage::class, $message = $messages[0]->getMessage());

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);

        $this->assertSame('en', $message->getLocale());
        $this->assertSame('titouan.galopin@citipo.com', $message->getEmail());
        $this->assertSame($orga->getId(), $message->getOrganizationId());
        $this->assertNull($message->getTagId());
    }

    public function testExportTag()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $tag = self::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);
        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/export?tag='.$tag->getId());

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags');
        $client->click($crawler->filter('tr:contains("ExampleTag") a:contains("Export contacts")')->link());

        // Should have published message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var ExportMessage $message */
        $this->assertInstanceOf(ExportMessage::class, $message = $messages[0]->getMessage());

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);

        $this->assertSame('en', $message->getLocale());
        $this->assertSame('titouan.galopin@citipo.com', $message->getEmail());
        $this->assertSame($orga->getId(), $message->getOrganizationId());
        $this->assertSame($tag->getId(), $message->getTagId());
    }
}
