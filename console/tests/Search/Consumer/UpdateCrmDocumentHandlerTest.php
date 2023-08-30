<?php

namespace App\Tests\Search\Consumer;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Repository\Community\ContactRepository;
use App\Search\Consumer\UpdateCrmDocumentHandler;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Tests\KernelTestCase;
use Doctrine\DBAL\Connection;

class UpdateCrmDocumentHandlerTest extends KernelTestCase
{
    public function testConsumeValid()
    {
        self::bootKernel();

        // Update contact
        static::getContainer()->get(Connection::class)->executeStatement('
            UPDATE community_contacts SET settings_receive_newsletters = FALSE WHERE email = \'troycovillon@teleworm.us\'
        ');

        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'troycovillon@teleworm.us']);

        // Apply handler
        $handler = static::getContainer()->get(UpdateCrmDocumentHandler::class);
        $handler(UpdateCrmDocumentsMessage::forContact($contact));

        // Check Meilisearch is updated
        $documents = static::getContainer()->get(MeilisearchInterface::class)->search(
            $contact->getOrganization()->getCrmIndexName(),
            'troycovillon@teleworm.us'
        );

        $this->assertTrue($documents['hits'][0]['settings_receive_newsletters']);
    }
}
