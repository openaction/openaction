<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Search\Consumer\AddTagCrmBatchMessage;
use App\Search\Consumer\ExportCrmBatchMessage;
use App\Search\Consumer\RemoveCrmBatchMessage;
use App\Search\Consumer\RemoveTagCrmBatchMessage;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ContactCrmControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();
    }

    public function testUpdateTags()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);

        // Update tags
        $client->request(
            'POST',
            '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/e90c2a1c-9504-497d-8354-c9dabc1ff7a2/update-tags',
            server: ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)],
            content: Json::encode([['id' => $tag->getId(), 'name' => $tag->getName(), 'slug' => $tag->getSlug()]]),
        );
        $this->assertResponseIsSuccessful();

        /*
         * Check database update
         */
        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['uuid' => 'e90c2a1c-9504-497d-8354-c9dabc1ff7a2']);
        $this->assertSame(['ExampleTag'], $contact->getMetadataTagsNames());

        /*
         * Check search engine update
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_indexing');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(UpdateCrmDocumentsMessage::class, $messages[0]->getMessage());
    }

    public function testBatchAddTag()
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ORGA_CITIPO_UUID]);
        $this->assertInstanceOf(Organization::class, $orga);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);

        $client->request(
            'POST',
            '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/batch/add-tag',
            server: ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)],
            content: Json::encode([
                'queryInput' => 'bru',
                'queryFilter' => ['status = \'m\''],
                'querySort' => ['profile_first_name:desc'],
                'params' => ['tagId' => $tag->getId()],
            ]),
        );

        $this->assertResponseIsSuccessful();
        $this->assertNotNull($statusUrl = Json::decode($client->getResponse()->getContent())['statusUrl'] ?? null);

        // Test the dispatching of the message
        $transport = self::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var AddTagCrmBatchMessage $message */
        $this->assertInstanceOf(AddTagCrmBatchMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($tag->getId(), $message->getTagId());
        $this->assertSame($orga->getId(), $message->getOrganizationId());
        $this->assertSame([
            'queryInput' => 'bru',
            'queryFilter' => ['status = \'m\''],
            'querySort' => ['profile_first_name:desc'],
            'params' => ['tagId' => $tag->getId()],
        ], $message->getBatchRequest());

        // Check status
        $client->request('GET', $statusUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSame(
            ['finished' => false, 'step' => 0, 'progress' => 0, 'payload' => []],
            Json::decode($client->getResponse()->getContent())
        );
    }

    public function testBatchRemoveTag()
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ORGA_CITIPO_UUID]);
        $this->assertInstanceOf(Organization::class, $orga);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'StartWithTag']);

        $client->request(
            'POST',
            '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/batch/remove-tag',
            server: ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)],
            content: Json::encode([
                'queryInput' => 'bru',
                'queryFilter' => ['status = \'m\''],
                'querySort' => ['profile_first_name:desc'],
                'params' => ['tagId' => $tag->getId()],
            ]),
        );

        $this->assertResponseIsSuccessful();
        $this->assertNotNull($statusUrl = Json::decode($client->getResponse()->getContent())['statusUrl'] ?? null);

        // Test the dispatching of the message
        $transport = self::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var RemoveTagCrmBatchMessage $message */
        $this->assertInstanceOf(RemoveTagCrmBatchMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($tag->getId(), $message->getTagId());
        $this->assertSame($orga->getId(), $message->getOrganizationId());
        $this->assertSame([
            'queryInput' => 'bru',
            'queryFilter' => ['status = \'m\''],
            'querySort' => ['profile_first_name:desc'],
            'params' => ['tagId' => $tag->getId()],
        ], $message->getBatchRequest());

        // Check status
        $client->request('GET', $statusUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSame(
            ['finished' => false, 'step' => 0, 'progress' => 0, 'payload' => []],
            Json::decode($client->getResponse()->getContent())
        );
    }

    public function testBatchRemove()
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ORGA_CITIPO_UUID]);
        $this->assertInstanceOf(Organization::class, $orga);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/batch/remove',
            server: ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)],
            content: Json::encode([
                'queryInput' => 'bru',
                'queryFilter' => ['status = \'m\''],
                'querySort' => ['profile_first_name:desc'],
            ]),
        );

        $this->assertResponseIsSuccessful();
        $this->assertNotNull($statusUrl = Json::decode($client->getResponse()->getContent())['statusUrl'] ?? null);

        // Test the dispatching of the message
        $transport = self::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var RemoveCrmBatchMessage $message */
        $this->assertInstanceOf(RemoveCrmBatchMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($orga->getId(), $message->getOrganizationId());
        $this->assertSame([
            'queryInput' => 'bru',
            'queryFilter' => ['status = \'m\''],
            'querySort' => ['profile_first_name:desc'],
            'params' => null,
        ], $message->getBatchRequest());

        // Check status
        $client->request('GET', $statusUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSame(
            ['finished' => false, 'step' => 0, 'progress' => 0, 'payload' => []],
            Json::decode($client->getResponse()->getContent())
        );
    }

    public function testBatchExport()
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ORGA_CITIPO_UUID]);
        $this->assertInstanceOf(Organization::class, $orga);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/batch/export',
            server: ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)],
            content: Json::encode([
                'queryInput' => 'bru',
                'queryFilter' => ['status = \'m\''],
                'querySort' => ['profile_first_name:desc'],
                'params' => [],
            ]),
        );

        $this->assertResponseIsSuccessful();
        $this->assertNotNull($statusUrl = Json::decode($client->getResponse()->getContent())['statusUrl'] ?? null);

        // Test the dispatching of the message
        $transport = self::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var ExportCrmBatchMessage $message */
        $this->assertInstanceOf(ExportCrmBatchMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($orga->getId(), $message->getOrganizationId());
        $this->assertSame([
            'queryInput' => 'bru',
            'queryFilter' => ['status = \'m\''],
            'querySort' => ['profile_first_name:desc'],
            'params' => [],
        ], $message->getBatchRequest());

        // Check status
        $client->request('GET', $statusUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSame(
            ['finished' => false, 'step' => 0, 'progress' => 0, 'payload' => []],
            Json::decode($client->getResponse()->getContent())
        );
    }
}
