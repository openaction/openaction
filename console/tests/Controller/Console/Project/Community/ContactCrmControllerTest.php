<?php

namespace App\Tests\Controller\Console\Project\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\TagRepository;
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

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();
    }

    public function testUpdateTags()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts');
        $this->assertResponseIsSuccessful();

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);

        // Update tags
        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/e90c2a1c-9504-497d-8354-c9dabc1ff7a2/update-tags',
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
}
